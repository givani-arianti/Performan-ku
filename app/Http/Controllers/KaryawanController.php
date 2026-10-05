<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Absensi;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\Progress;
use App\Models\Komentar;
use App\Models\User;
use App\Models\PengajuanIzin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class KaryawanController extends Controller
{
    /**
     * Determine attendance status from a given time.
     */
    private function getAbsensiInfo(string $jenis, string $jam): array
    {
        $jamDate = \Carbon\Carbon::createFromFormat('H:i:s', $jam);

        if ($jenis === 'masuk') {
            $batasTerlambat = \Carbon\Carbon::createFromFormat('H:i:s', '09:05:00');

            if ($jamDate->greaterThan($batasTerlambat)) {
                $selisihMenit = (int) round(abs($jamDate->diffInMinutes($batasTerlambat, false)));

                return [
                    'label' => 'Terlambat',
                    'detail' => 'Terlambat ' . $selisihMenit . ' menit',
                    'color' => 'bg-red-50 text-red-700 border-red-200',
                ];
            }

            return [
                'label' => 'Tepat waktu',
                'detail' => 'Masuk tepat waktu',
                'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            ];
        }

        $batasPulang = \Carbon\Carbon::createFromFormat('H:i:s', '17:00:00');

        if ($jamDate->greaterThanOrEqualTo($batasPulang)) {
            return [
                'label' => 'Pulang sesuai jadwal',
                'detail' => 'Pulang tepat waktu',
                'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            ];
        }

        return [
            'label' => 'Pulang lebih awal',
            'detail' => 'Pulang sebelum jam 17:00',
            'color' => 'bg-amber-50 text-amber-700 border-amber-200',
        ];
    }

    /**
     * Display the employee's dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tasks = $user->assignedTasks()->with(['progress', 'evaluasi'])->get();

        // Calculate statistics
        $totalTasks = $tasks->count();
        $pendingTasks = $tasks->where('status', 'Pending')->count();
        $inProgressTasks = $tasks->where('status', 'In Progress')->count();
        $completedTasks = $tasks->whereIn('status', ['Completed', 'Reviewed'])->count();

        $kpiDetail = $user->calculatePerformanceKpi($tasks);
        $averageScore = $kpiDetail['final_score'];

        // Get 5 recent tasks
        $recentTasks = $user->assignedTasks()
            ->with(['progress'])
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        $rekanKerja = User::where('role', 'karyawan')
            ->whereKeyNot($user->id)
            ->orderBy('name')
            ->get();
        $komentarDiterima = $user->komentarDiterima()
            ->latest()
            ->limit(10)
            ->get();

        return view('karyawan.dashboard', compact(
            'totalTasks',
            'pendingTasks',
            'inProgressTasks',
            'completedTasks',
            'averageScore',
            'kpiDetail',
            'recentTasks',
            'rekanKerja',
            'komentarDiterima'
        ));
    }

    public function storeKomentar(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'penerima_id' => ['required', 'integer', 'exists:users,id', 'different:' . $user->id],
            'komentar' => ['required', 'string', 'max:1000'],
        ]);

        $recipient = User::where('role', 'karyawan')->findOrFail($validated['penerima_id']);

        Komentar::create([
            'pengirim_id' => $user->id,
            'penerima_id' => $recipient->id,
            'komentar' => $validated['komentar'],
        ]);

        return redirect()->route('karyawan.dashboard')
            ->with('success', 'Komentar berhasil dikirim kepada ' . $recipient->name . '.');
    }

    /**
     * Show attendance page for employee.
     */
    public function absensi(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $todayLogs = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->get();

        $checkInRecord = $todayLogs->firstWhere('jenis', 'masuk');
        $checkOutRecord = $todayLogs->firstWhere('jenis', 'pulang');

        $checkIn = $checkInRecord ? (string) $checkInRecord->jam : null;
        $checkOut = $checkOutRecord ? (string) $checkOutRecord->jam : null;

        $status = [
            'label' => 'Belum absen',
            'detail' => 'Silakan absen masuk terlebih dahulu',
            'color' => 'bg-slate-100 text-slate-700 border-slate-200',
        ];

        if ($checkIn) {
            $status = $this->getAbsensiInfo('masuk', $checkIn);
        }

        if ($checkIn && $checkOut) {
            $status = $this->getAbsensiInfo('pulang', $checkOut);
        }

        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));

        $recentAbsensi = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->tanggal,
                    'jenis' => $item->jenis,
                    'jam' => (string) $item->jam,
                    'status' => $item->status,
                ];
            })
            ->all();

        $pengajuanIzinList = PengajuanIzin::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('karyawan.absensi', compact('status', 'checkIn', 'checkOut', 'recentAbsensi', 'pengajuanIzinList', 'bulan', 'tahun'));
    }

    /**
     * Store employee leave / permission request.
     */
    public function storePengajuanIzin(Request $request)
    {
        $request->validate([
            'jenis' => ['required', 'in:cuti,izin,sakit'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['required', 'string', 'max:1000'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $user = Auth::user();
        $dokumenPath = null;

        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')->store('dokumen_izin', 'public');
        }

        PengajuanIzin::create([
            'user_id' => $user->id,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'dokumen' => $dokumenPath,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.absensi')->with('success', 'Pengajuan ' . ucfirst($request->jenis) . ' berhasil dikirim dan menantikan persetujuan.');
    }

    /**
     * Save employee attendance record.
     */
    public function storeAbsensi(Request $request)
    {
        $request->validate([
            'jenis' => ['required', 'in:masuk,pulang'],
        ]);

        $user = Auth::user();
        $today = now()->toDateString();
        
        // Gunakan jam dari perangkat (laptop/HP) jika ada, jika tidak gunakan waktu server
        $jam = $request->filled('client_time') ? $request->client_time : now()->format('H:i:s');
        
        $info = $this->getAbsensiInfo($request->jenis, $jam);

        $todayLogs = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->get();

        if ($request->jenis === 'masuk') {
            if ($todayLogs->contains('jenis', 'masuk')) {
                return redirect()->route('karyawan.absensi')->with('error', 'Anda sudah melakukan absen masuk hari ini.');
            }
        }

        if ($request->jenis === 'pulang') {
            if (!$todayLogs->contains('jenis', 'masuk')) {
                return redirect()->route('karyawan.absensi')->with('error', 'Anda belum absen masuk hari ini.');
            }

            if ($todayLogs->contains('jenis', 'pulang')) {
                return redirect()->route('karyawan.absensi')->with('error', 'Anda sudah melakukan absen pulang hari ini.');
            }
        }

        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'jenis' => $request->jenis,
            'jam' => $jam,
            'status' => $info['label'],
            'detail' => $info['detail'],
        ]);

        $label = $request->jenis === 'masuk' ? 'Masuk' : 'Pulang';

        return redirect()->route('karyawan.absensi')->with('success', "Absen {$label} {$info['detail']} berhasil dicatat.");
    }

    /**
     * List all tasks assigned to the employee.
     */
    public function tasks(Request $request)
    {
        $user = Auth::user();
        $query = $user->assignedTasks()->with(['progress', 'evaluasi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('deadline', 'asc')->get();

        return view('karyawan.tasks', compact('tasks'));
    }

    /**
     * Display the details of an assigned task.
     */
    public function taskDetail($id)
    {
        $user = Auth::user();
        $task = Task::with(['checklists', 'progress' => function($q) {
            $q->orderBy('tanggal_update', 'desc');
        }, 'evaluasi.manajer'])->findOrFail($id);

        // Ensure user owns this task
        if ($task->id_karyawan !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        return view('karyawan.task_detail', compact('task'));
    }

    /**
     * Toggle checklist item state for a task.
     */
    public function toggleChecklist(Request $request, $taskId, $checklistId)
    {
        $user = Auth::user();
        $task = Task::with('checklists')->findOrFail($taskId);

        if ($task->id_karyawan !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $checklist = TaskChecklist::where('task_id', $task->id)->findOrFail($checklistId);
        $checklist->is_completed = !$checklist->is_completed;
        $checklist->save();

        if ($task->status === 'Pending' && $checklist->is_completed) {
            $task->status = 'In Progress';
            $task->save();
        }

        return redirect()->route('karyawan.task.detail', $task->id)
            ->with('success', 'Status checklist berhasil diperbarui.');
    }

    /**
     * Submit final work and complete task.
     */
    public function submitWork(Request $request, $id)
    {
        $user = Auth::user();
        $task = Task::findOrFail($id);

        if ($task->id_karyawan !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $documentPath = $request->file('dokumen')->store('hasil-tugas', 'public');

        // Create 100% progress log
        Progress::create([
            'task_id' => $task->id,
            'persentase' => 100,
            'keterangan' => 'Tugas selesai dan file hasil kerja telah dikirim.',
            'tanggal_update' => now(),
        ]);

        // Update task status and final work text
        $task->status = 'Completed';
        if ($task->dokumen_path) {
            Storage::disk('public')->delete($task->dokumen_path);
        }
        $task->hasil_kerja = $request->file('dokumen')->getClientOriginalName();
        $task->dokumen_path = $documentPath;
        $task->save();

        return redirect()->route('karyawan.task.detail', $task->id)
            ->with('success', 'Tugas berhasil diselesaikan dan dikirim ke sistem untuk dievaluasi.');
    }
}
