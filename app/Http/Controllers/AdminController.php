<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Absensi;
use App\Models\PengajuanIzin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display Admin (HR) dashboard - monitoring only.
     */
    public function dashboard()
    {
        $totalEmployees = User::where('role', 'karyawan')->count();
        $totalManagers = User::where('role', 'manajer')->count();
        $tasks = Task::with(['progress', 'evaluasi'])->get();
        $totalTasks = $tasks->count();
        
        $pendingTasks = $tasks->where('status', 'Pending')->count();
        $inProgressTasks = $tasks->where('status', 'In Progress')->count();
        $completedTasks = $tasks->where('status', 'Completed')->count();
        $reviewedTasks = $tasks->where('status', 'Reviewed')->count();

        $averageScore = User::where('role', 'karyawan')->get()
            ->map(fn ($employee) => $employee->calculatePerformanceKpi()['final_score'])
            ->avg() ?? 0;

        // Recent tasks
        $recentTasks = Task::with('karyawan')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Absensi statistics for today
        $today = now()->toDateString();
        $todayAbsensi = Absensi::where('tanggal', $today)->where('jenis', 'masuk')->get();
        $presentCount = $todayAbsensi->pluck('user_id')->unique()->count();
        $onTimeCount = $todayAbsensi->where('status', 'Tepat waktu')->count();
        $lateCount = $todayAbsensi->where('status', 'Terlambat')->count();
        $notCheckedInCount = max(0, $totalEmployees - $presentCount);

        // Recent absensi logs
        $recentAbsensi = Absensi::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Employee of the Month (highest KPI score)
        $allKaryawan = User::where('role', 'karyawan')->get();
        $topEmployee = $allKaryawan->map(function ($emp) {
            $kpi = $emp->calculatePerformanceKpi();
            return (object) [
                'id' => $emp->id,
                'name' => $emp->name,
                'email' => $emp->email,
                'divisi' => $emp->divisi,
                'score' => $kpi['final_score'],
                'category' => $kpi['category'],
            ];
        })->sortByDesc('score')->first();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'totalManagers',
            'totalTasks',
            'pendingTasks',
            'inProgressTasks',
            'completedTasks',
            'reviewedTasks',
            'averageScore',
            'recentTasks',
            'presentCount',
            'onTimeCount',
            'lateCount',
            'notCheckedInCount',
            'recentAbsensi',
            'topEmployee'
        ));
    }

    /**
     * Display Employee Attendance Recap for HR / Admin.
     */
    public function absensi(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));
        $userId = $request->input('user_id');
        $tanggal = $request->input('tanggal');

        $employees = User::where('role', 'karyawan')->orderBy('name', 'asc')->get();

        $query = Absensi::with('user')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        $absensiList = $query->orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->get();

        $pendingIzinList = PengajuanIzin::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalMasuk = $absensiList->where('jenis', 'masuk')->count();
        $totalTepatWaktu = $absensiList->where('jenis', 'masuk')->where('status', 'Tepat waktu')->count();
        $totalTerlambat = $absensiList->where('jenis', 'masuk')->where('status', 'Terlambat')->count();
        $totalPulang = $absensiList->where('jenis', 'pulang')->count();

        return view('admin.absensi', compact(
            'absensiList',
            'pendingIzinList',
            'employees',
            'bulan',
            'tahun',
            'userId',
            'tanggal',
            'totalMasuk',
            'totalTepatWaktu',
            'totalTerlambat',
            'totalPulang'
        ));
    }

    /**
     * Approve employee leave/permission request.
     */
    public function approveIzin($id)
    {
        $izin = PengajuanIzin::findOrFail($id);
        $izin->status = 'approved';
        $izin->disetujui_oleh = Auth::id();
        $izin->save();

        // Create attendance records for approved range
        $curr = \Carbon\Carbon::parse($izin->tanggal_mulai);
        $end = \Carbon\Carbon::parse($izin->tanggal_selesai);

        while ($curr->lte($end)) {
            Absensi::updateOrCreate(
                [
                    'user_id' => $izin->user_id,
                    'tanggal' => $curr->toDateString(),
                    'jenis' => 'masuk',
                ],
                [
                    'jam' => '08:00:00',
                    'status' => ucfirst($izin->jenis),
                    'detail' => 'Disetujui: ' . $izin->alasan,
                ]
            );
            $curr->addDay();
        }

        return redirect()->back()->with('success', 'Pengajuan ' . ucfirst($izin->jenis) . ' berhasil disetujui.');
    }

    /**
     * Reject employee leave/permission request.
     */
    public function rejectIzin(Request $request, $id)
    {
        $izin = PengajuanIzin::findOrFail($id);
        $izin->status = 'rejected';
        $izin->disetujui_oleh = Auth::id();
        $izin->catatan_approver = $request->input('catatan_approver');
        $izin->save();

        return redirect()->back()->with('success', 'Pengajuan ' . ucfirst($izin->jenis) . ' telah ditolak.');
    }

    /**
     * Monitoring - view all tasks and employee performance (READ ONLY).
     */
    public function monitoring(Request $request)
    {
        $selectedDivisi = $request->input('divisi');

        $divisiList = User::whereNotNull('divisi')
            ->where('divisi', '!=', '')
            ->pluck('divisi')
            ->unique()
            ->values();

        $taskQuery = Task::with(['karyawan', 'hr', 'progress', 'evaluasi'])
            ->orderBy('created_at', 'desc');

        $empQuery = User::where('role', 'karyawan')
            ->with(['assignedTasks.evaluasi', 'assignedTasks.progress']);

        if ($selectedDivisi) {
            $empQuery->where('divisi', $selectedDivisi);
            $taskQuery->whereHas('karyawan', function($q) use ($selectedDivisi) {
                $q->where('divisi', $selectedDivisi);
            });
        }

        $tasks = $taskQuery->get();
        $employees = $empQuery->get();

        $employeeData = $employees->map(function($emp) {
            $tasks = $emp->assignedTasks;
            $totalTasks = $tasks->count();
            $completed = $tasks->whereIn('status', ['Completed', 'Reviewed'])->count();
            $avg = $emp->calculatePerformanceKpi($tasks)['final_score'];

            return (object) [
                'id' => $emp->id,
                'name' => $emp->name,
                'email' => $emp->email,
                'divisi' => $emp->divisi,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completed,
                'avg_score' => $avg,
            ];
        })->sortByDesc('avg_score')->values();

        return view('admin.monitoring', compact('tasks', 'employeeData', 'divisiList', 'selectedDivisi'));
    }

    /**
     * Display list of users (Karyawan & Manajer).
     */
    public function users()
    {
        $users = User::whereIn('role', ['karyawan', 'manajer'])
            ->orderBy('role', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Store new user account.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', 'in:karyawan,manajer'],
            'divisi' => ['nullable', 'string', 'max:100'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'divisi' => $request->divisi,
            'password' => Hash::make('password123'), // Default password
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Akun pengguna berhasil dibuat dengan password bawaan: password123.');
    }

    /**
     * Update user details.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:karyawan,manajer'],
            'divisi' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->divisi = $request->divisi;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    /**
     * Delete user account.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
