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
use App\Services\AttendanceService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $tasks = $user->assignedTasks()->with(['progress', 'evaluasi', 'checklists'])->get();

        // Calculate statistics
        $totalTasks = $tasks->count();
        $pendingTasks = $tasks->where('status', 'Pending')->count();
        $inProgressTasks = $tasks->where('status', 'In Progress')->count();
        $revisiTasks = $tasks->where('status', 'Revisi')->count();
        $completedTasks = $tasks->whereIn('status', ['Completed', 'Reviewed'])->count();

        $kpiDetail = $user->calculatePerformanceKpi(
            $tasks->filter(fn ($task) => \Carbon\Carbon::parse($task->deadline)->isCurrentMonth())
        );
        $averageScore = $kpiDetail['final_score'];

        // Get 5 recent tasks
        $recentTasks = $user->assignedTasks()
            ->with(['progress', 'evaluasi', 'checklists'])
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
            'revisiTasks',
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
            $dokumenPath = $request->file('dokumen')->store('dokumen_izin', 'local');
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
        $jam = now()->format('H:i:s');
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

        try {
            Absensi::create([
                'user_id' => $user->id,
                'tanggal' => $today,
                'jenis' => $request->jenis,
                'jam' => $jam,
                'status' => $info['label'],
                'detail' => $info['detail'],
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            return redirect()->route('karyawan.absensi')
                ->with('error', 'Absensi hari ini sudah tercatat. Silakan muat ulang halaman.');
        }

        $label = $request->jenis === 'masuk' ? 'Masuk' : 'Pulang';

        return redirect()->route('karyawan.absensi')->with('success', "Absen {$label} {$info['detail']} berhasil dicatat.");
    }

    /**
     * List all tasks assigned to the employee with monthly filtering & summary.
     */
    public function tasks(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));
        $status = $request->input('status');

        $query = $user->assignedTasks()->with(['progress', 'evaluasi', 'checklists']);

        if ($bulan && $bulan !== 'all') {
            $query->whereMonth('deadline', $bulan)->whereYear('deadline', $tahun);
        } elseif ($tahun && $tahun !== 'all') {
            $query->whereYear('deadline', $tahun);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tasks = $query->orderBy('deadline', 'asc')->get();

        // Calculate monthly summary for this month/year
        $monthQuery = $user->assignedTasks()->with(['progress', 'checklists']);
        if ($bulan && $bulan !== 'all') {
            $monthQuery->whereMonth('deadline', $bulan)->whereYear('deadline', $tahun);
        } elseif ($tahun && $tahun !== 'all') {
            $monthQuery->whereYear('deadline', $tahun);
        }
        $allMonthTasks = $monthQuery->get();

        $monthlySummary = [
            'total' => $allMonthTasks->count(),
            'completed' => $allMonthTasks->whereIn('status', ['Completed', 'Reviewed'])->count(),
            'revisi' => $allMonthTasks->where('status', 'Revisi')->count(),
            'in_progress' => $allMonthTasks->whereIn('status', ['Pending', 'In Progress'])->count(),
            'avg_score' => $allMonthTasks->count() > 0 ? round($allMonthTasks->avg(fn($t) => $t->persentase)) : 0,
            'on_time' => $allMonthTasks->filter(fn($t) => $t->kecepatan_info['is_on_time'])->count(),
        ];

        return view('karyawan.tasks', compact('tasks', 'monthlySummary', 'bulan', 'tahun'));
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

        if (!in_array($task->status, ['Pending', 'In Progress', 'Revisi'])) {
            return redirect()->route('karyawan.task.detail', $task->id)
                ->with('error', 'Checklist tidak dapat diubah karena tugas sudah dikirim atau dievaluasi.');
        }

        $checklist = TaskChecklist::where('task_id', $task->id)->findOrFail($checklistId);
        $checklist->is_completed = !$checklist->is_completed;
        $checklist->save();

        $task->forgetComputedAttributes();
        $task->load('checklists');

        if (in_array($task->status, ['Pending', 'In Progress'])) {
            $task->status = $task->checklists->contains(fn ($item) => $item->is_completed)
                ? 'In Progress'
                : 'Pending';
            $task->save();
        }

        return redirect()->route('karyawan.task.detail', $task->id)
            ->with('success', 'Status checklist berhasil diperbarui.');
    }

    /**
     * Submit final work or revision and complete task.
     */
    public function submitWork(Request $request, $id)
    {
        $user = Auth::user();
        $task = Task::with('checklists')->findOrFail($id);

        if ($task->id_karyawan !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($task->status, ['Pending', 'In Progress', 'Revisi'])) {
            return redirect()->route('karyawan.task.detail', $task->id)
                ->with('error', 'Tugas ini sudah dikirim dan tidak dapat dikirim ulang.');
        }

        $request->validate([
            'dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($task->checklists->isNotEmpty() && $task->checklists->contains(fn ($item) => ! $item->is_completed)) {
            return redirect()->route('karyawan.task.detail', $task->id)
                ->with('error', 'Selesaikan seluruh poin checklist sebelum mengirim hasil kerja.');
        }

        $documentPath = $request->file('dokumen')->store('hasil-tugas', 'public');
        $isRevision = $task->status === 'Revisi';

        DB::transaction(function () use ($task, $request, $documentPath, $isRevision) {
            Progress::create([
                'task_id' => $task->id,
                'persentase' => 100,
                'keterangan' => $isRevision
                    ? 'Hasil revisi tugas telah diunggah dan dikirimkan kembali ke manajer untuk dievaluasi.'
                    : 'Tugas selesai dan file hasil kerja telah dikirim.',
                'tanggal_update' => now(),
            ]);

            if ($task->dokumen_path) {
                Storage::disk('public')->delete($task->dokumen_path);
            }

            $task->status = 'Completed';
            $task->hasil_kerja = $request->file('dokumen')->getClientOriginalName();
            $task->dokumen_path = $documentPath;
            $task->save();
        });

        $task->forgetComputedAttributes();

        $msg = $isRevision
            ? 'Hasil revisi tugas berhasil dikirim kembali ke manajer untuk ditinjau dan dievaluasi.'
            : 'Tugas berhasil diselesaikan dan dikirim ke sistem untuk dievaluasi.';

        return redirect()->route('karyawan.task.detail', $task->id)
            ->with('success', $msg);
    }

    /**
     * Download a leave/permission supporting document.
     * Documents live on the private disk and are only served to the owner or HR.
     */
    public function downloadDokumenIzin($id)
    {
        $izin = PengajuanIzin::findOrFail($id);
        $user = Auth::user();

        if ($izin->user_id !== $user->id && !in_array($user->role, ['admin', 'hr'], true)) {
            abort(403, 'Akses ditolak.');
        }

        if (!$izin->dokumen) {
            abort(404);
        }

        if (Storage::disk('local')->exists($izin->dokumen)) {
            return Storage::disk('local')->download($izin->dokumen);
        }

        // Backward compatibility for files uploaded before the private-disk change.
        if (Storage::disk('public')->exists($izin->dokumen)) {
            return Storage::disk('public')->download($izin->dokumen);
        }

        abort(404);
    }

    /**
     * Display personal performance and task report for employee.
     */
    public function laporan(Request $request, AttendanceService $attendance)
    {
        $user = Auth::user();
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));

        $taskQuery = $user->assignedTasks()->with(['checklists', 'evaluasi', 'progress']);
        if ($bulan && $bulan !== 'all') {
            $taskQuery->whereMonth('deadline', $bulan)->whereYear('deadline', $tahun);
        } elseif ($tahun && $tahun !== 'all') {
            $taskQuery->whereYear('deadline', $tahun);
        }
        $tasks = $taskQuery->orderBy('deadline', 'asc')->get();

        $kpi = $user->calculatePerformanceKpi($tasks, $bulan === 'all' ? now()->month : (int)$bulan, (int)$tahun);

        $absensiList = $attendance->recordsFor($user->id, $bulan, $tahun);
        $attendanceSummary = $attendance->summarize($absensiList, $user->id, $bulan, $tahun);

        $totalHadir = $attendanceSummary['total_hadir'];
        $tepatWaktu = $attendanceSummary['tepat_waktu'];
        $terlambat = $attendanceSummary['terlambat'];
        $izinCount = $attendanceSummary['izin_hari'];

        return view('karyawan.laporan', compact(
            'user',
            'tasks',
            'kpi',
            'bulan',
            'tahun',
            'totalHadir',
            'tepatWaktu',
            'terlambat',
            'izinCount'
        ));
    }
}
