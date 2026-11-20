<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Task;
use App\Models\Evaluasi;
use App\Models\TaskChecklist;
use App\Models\Progress;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManajerController extends Controller
{
    /**
     * Display Manager dashboard.
     */
    public function dashboard()
    {
        $manager = Auth::user();
        $managerDivisi = $manager->divisi;
        $month = now()->month;
        $year = now()->year;

        $empQuery = User::where('role', 'karyawan')
            ->with([
                'assignedTasks' => fn ($query) => $query
                    ->whereMonth('deadline', $month)
                    ->whereYear('deadline', $year)
                    ->with(['checklists', 'progress']),
            ]);
        $taskQuery = Task::with(['karyawan', 'progress', 'evaluasi']);

        if ($managerDivisi) {
            $empQuery->where('divisi', $managerDivisi);
            $taskQuery->where(function($q) use ($managerDivisi) {
                $q->where('divisi', $managerDivisi)
                  ->orWhereHas('karyawan', fn($k) => $k->where('divisi', $managerDivisi));
            });
        }

        $employees = $empQuery->get();
        $totalEmployees = $employees->count();

        $tasks = $taskQuery->get();
        $totalTasks = $tasks->count();
        $reviewedTasks = $tasks->where('status', 'Reviewed')->count();
        $revisiTasksCount = $tasks->where('status', 'Revisi')->count();

        $pendingEvaluations = Task::where('status', 'Completed')
            ->when($managerDivisi, function($q) use ($managerDivisi) {
                $q->where(function($sub) use ($managerDivisi) {
                    $sub->where('divisi', $managerDivisi)
                        ->orWhereHas('karyawan', fn($k) => $k->where('divisi', $managerDivisi));
                });
            })
            ->with(['karyawan', 'progress'])
            ->orderBy('updated_at', 'asc')
            ->get();

        $averageScore = $employees
            ->map(fn ($employee) => $employee->calculatePerformanceKpi($employee->assignedTasks, $month, $year)['final_score'])
            ->avg() ?? 0;

        // Get employee list with scores for leaderboard
        $employeeScores = $employees->map(function($emp) use ($month, $year) {
            $tasks = $emp->assignedTasks;
            $avg = $emp->calculatePerformanceKpi($tasks, $month, $year)['final_score'];
            return (object) [
                'name' => $emp->name,
                'email' => $emp->email,
                'avg_score' => $avg,
                'tasks_count' => $tasks->count()
            ];
        })->sortByDesc('avg_score')->take(5);

        return view('manajer.dashboard', compact(
            'pendingEvaluations',
            'totalEmployees',
            'reviewedTasks',
            'revisiTasksCount',
            'averageScore',
            'employeeScores',
            'managerDivisi'
        ));
    }

    // =============================================
    // TASK MANAGEMENT (Manajer mengelola tugas)
    // =============================================

    /**
     * List all tasks with filter options and monthly summary.
     */
    public function tasks(Request $request)
    {
        $manager = Auth::user();
        $managerDivisi = $manager->divisi;

        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));
        $status = $request->input('status');
        $karyawanId = $request->input('karyawan_id');
        $divisi = $request->input('divisi');

        $query = Task::with(['karyawan', 'hr', 'progress', 'checklists'])->orderBy('created_at', 'desc');

        if ($managerDivisi) {
            $query->where(function($q) use ($managerDivisi) {
                $q->where('divisi', $managerDivisi)
                  ->orWhereHas('karyawan', fn($k) => $k->where('divisi', $managerDivisi));
            });
        } elseif ($divisi) {
            $query->where('divisi', $divisi);
        }

        if ($karyawanId) {
            $query->where('id_karyawan', $karyawanId);
        }

        if ($bulan && $bulan !== 'all') {
            $query->whereMonth('deadline', $bulan)->whereYear('deadline', $tahun);
        } elseif ($tahun && $tahun !== 'all') {
            $query->whereYear('deadline', $tahun);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $allTasks = $query->get();

        $activeTasks = $allTasks->whereIn('status', ['Pending', 'In Progress']);
        $revisiTasks = $allTasks->where('status', 'Revisi');
        $completedTasks = $allTasks->whereIn('status', ['Completed', 'Reviewed']);

        // Monthly Summary
        $monthlySummary = [
            'total' => $allTasks->count(),
            'completed' => $allTasks->whereIn('status', ['Completed', 'Reviewed'])->count(),
            'revisi' => $allTasks->where('status', 'Revisi')->count(),
            'in_progress' => $allTasks->whereIn('status', ['Pending', 'In Progress'])->count(),
            'on_time' => $allTasks->filter(fn($t) => $t->kecepatan_info['is_on_time'])->count(),
        ];

        // Filter employees to manager's division if manager has a division
        $empQuery = User::where('role', 'karyawan')->orderBy('name', 'asc');
        if ($managerDivisi) {
            $empQuery->where('divisi', $managerDivisi);
        }
        $employees = $empQuery->get();

        $divisiList = Task::whereNotNull('divisi')->pluck('divisi')->unique();

        return view('manajer.tasks', compact(
            'activeTasks', 
            'revisiTasks', 
            'completedTasks', 
            'monthlySummary', 
            'employees', 
            'divisiList', 
            'managerDivisi',
            'bulan',
            'tahun',
            'karyawanId',
            'divisi',
            'status'
        ));
    }

    /**
     * Store new task.
     */
    public function storeTask(Request $request)
    {
        $manager = Auth::user();
        $managerDivisi = $manager->divisi;

        $request->validate([
            'judul_tugas' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'target' => ['required'],
            'divisi' => ['nullable', 'string', 'max:100'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'id_karyawan' => ['required', 'exists:users,id'],
            'checklists' => ['nullable', 'array'],
            'checklists.*' => ['nullable', 'string', 'max:255'],
        ]);

        // Validasi dan format target maksimal 100%
        $targetInput = trim((string)$request->target);
        if (preg_match('/(\d+)/', $targetInput, $matches)) {
            $targetVal = (int) $matches[1];
            if ($targetVal > 100) {
                return redirect()->back()->withInput()->withErrors(['target' => 'Target pekerjaan tidak boleh lebih dari 100%.']);
            }
            $targetFormatted = $targetVal . '%';
        } else {
            $targetFormatted = '100%';
        }

        $assignedUser = User::findOrFail($request->id_karyawan);
        if ($assignedUser->role !== 'karyawan') {
            return redirect()->back()->withInput()->withErrors(['id_karyawan' => 'Tugas hanya dapat ditugaskan kepada karyawan.']);
        }

        if ($managerDivisi && $assignedUser->divisi && $assignedUser->divisi !== $managerDivisi) {
            return redirect()->back()->withInput()->with('error', 'Anda hanya dapat menugaskan karyawan dari divisi Anda (' . $managerDivisi . ').');
        }

        $divisiToAssign = $managerDivisi ?: ($request->divisi ?: $assignedUser->divisi);

        DB::transaction(function () use ($request, $targetFormatted, $divisiToAssign, $assignedUser) {
            $task = Task::create([
                'judul_tugas' => $request->judul_tugas,
                'deskripsi' => $request->deskripsi,
                'target' => $targetFormatted,
                'divisi' => $divisiToAssign,
                'deadline' => $request->deadline,
                'id_karyawan' => $assignedUser->id,
                'id_hr' => Auth::id(),
                'status' => 'Pending',
            ]);

            $checklists = array_unique(array_filter(array_map('trim', $request->input('checklists', []))));

            foreach ($checklists as $item) {
                TaskChecklist::create([
                    'task_id' => $task->id,
                    'judul' => $item,
                    'is_completed' => false,
                ]);
            }
        });

        return redirect()->route('manajer.tasks')
            ->with('success', 'Tugas dan checklist berhasil dibuat dan ditugaskan kepada ' . $assignedUser->name . '.');
    }

    /**
     * Update task.
     */
    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update', $task);

        $manager = Auth::user();
        $managerDivisi = $manager->divisi;

        $request->validate([
            'judul_tugas' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'target' => ['required'],
            'divisi' => ['nullable', 'string', 'max:100'],
            'deadline' => ['required', 'date'],
            'id_karyawan' => ['required', 'exists:users,id'],
            'status' => ['required', 'string', 'in:Pending,In Progress,Completed,Reviewed,Revisi'],
            'checklists' => ['nullable', 'array'],
            'checklists.*' => ['nullable', 'string', 'max:255'],
        ]);

        // Validasi dan format target maksimal 100%
        $targetInput = trim((string)$request->target);
        if (preg_match('/(\d+)/', $targetInput, $matches)) {
            $targetVal = (int) $matches[1];
            if ($targetVal > 100) {
                return redirect()->back()->withInput()->withErrors(['target' => 'Target pekerjaan tidak boleh lebih dari 100%.']);
            }
            $targetFormatted = $targetVal . '%';
        } else {
            $targetFormatted = '100%';
        }

        $assignedUser = User::findOrFail($request->id_karyawan);
        if ($assignedUser->role !== 'karyawan') {
            return redirect()->back()->withInput()->withErrors(['id_karyawan' => 'Tugas hanya dapat ditugaskan kepada karyawan.']);
        }

        if ($managerDivisi && $assignedUser->divisi && $assignedUser->divisi !== $managerDivisi) {
            return redirect()->back()->withInput()->with('error', 'Anda hanya dapat menugaskan karyawan dari divisi Anda (' . $managerDivisi . ').');
        }

        $divisiToAssign = $managerDivisi ?: ($request->divisi ?: $assignedUser->divisi);

        DB::transaction(function () use ($request, $task, $targetFormatted, $divisiToAssign, $assignedUser) {
            $task->update([
                'judul_tugas' => $request->judul_tugas,
                'deskripsi' => $request->deskripsi,
                'target' => $targetFormatted,
                'divisi' => $divisiToAssign,
                'deadline' => $request->deadline,
                'id_karyawan' => $assignedUser->id,
                'status' => $request->status,
            ]);

            $newChecklists = array_unique(array_filter(array_map('trim', $request->input('checklists', []))));

            foreach ($task->checklists as $existing) {
                if (!in_array($existing->judul, $newChecklists, true)) {
                    $existing->delete();
                }
            }

            $existingTitles = $task->checklists()->pluck('judul')->all();
            foreach ($newChecklists as $title) {
                if (!in_array($title, $existingTitles, true)) {
                    TaskChecklist::create([
                        'task_id' => $task->id,
                        'judul' => $title,
                        'is_completed' => false,
                    ]);
                }
            }
        });

        return redirect()->route('manajer.tasks')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Delete task.
     */
    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('manajer.tasks')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    // =============================================
    // PERFORMANCE & EVALUATION
    // =============================================

    /**
     * List employee performance data with monthly filter.
     */
    public function performance(Request $request)
    {
        $manager = Auth::user();
        $managerDivisi = $manager->divisi;

        $selectedDivisi = $managerDivisi ?: $request->input('divisi');
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));

        $divisiList = User::whereNotNull('divisi')
            ->where('divisi', '!=', '')
            ->pluck('divisi')
            ->unique()
            ->values();

        $employees = User::where('role', 'karyawan')
            ->when($selectedDivisi, fn ($query) => $query->where('divisi', $selectedDivisi))
            ->orderBy('name')
            ->get();

        $tasksByEmployee = Task::with(['evaluasi', 'progress', 'checklists'])
            ->whereIn('id_karyawan', $employees->pluck('id'))
            ->when($bulan && $bulan !== 'all', function ($query) use ($bulan, $tahun) {
                $query->whereMonth('deadline', $bulan)->whereYear('deadline', $tahun);
            })
            ->when((! $bulan || $bulan === 'all') && $tahun && $tahun !== 'all', function ($query) use ($tahun) {
                $query->whereYear('deadline', $tahun);
            })
            ->get()
            ->groupBy('id_karyawan');

        $employeeData = $employees->map(function($emp) use ($tasksByEmployee, $bulan, $tahun) {
            $tasks = $tasksByEmployee->get($emp->id, collect());
            $totalTasks = $tasks->count();
            $completed = $tasks->whereIn('status', ['Completed', 'Reviewed'])->count();
            $revisi = $tasks->where('status', 'Revisi')->count();

            $kpi = $emp->calculatePerformanceKpi(
                $tasks,
                $bulan === 'all' ? now()->month : (int) $bulan,
                (int) $tahun
            );

            return (object) [
                'id' => $emp->id,
                'name' => $emp->name,
                'email' => $emp->email,
                'divisi' => $emp->divisi,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completed,
                'revisi_tasks' => $revisi,
                'avg_score' => $kpi['final_score'],
                'category' => $kpi['category'],
            ];
        })->sortByDesc('avg_score')->values();

        return view('manajer.performance', compact('employeeData', 'divisiList', 'selectedDivisi', 'managerDivisi', 'bulan', 'tahun'));
    }

    /**
     * Detail view of single employee performance.
     */
    /**
     * Detail view of single employee performance & comprehensive printable report.
     */
    public function employeeDetail($id, Request $request, AttendanceService $attendance)
    {
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));

        $employee = User::where('role', 'karyawan')->findOrFail($id);
        $this->authorize('viewPerformance', $employee);

        $taskQuery = $employee->assignedTasks()
            ->with(['checklists', 'evaluasi', 'progress']);

        if ($bulan && $bulan !== 'all') {
            $taskQuery->whereMonth('deadline', $bulan)->whereYear('deadline', $tahun);
        } elseif ($tahun && $tahun !== 'all') {
            $taskQuery->whereYear('deadline', $tahun);
        }

        $filteredTasks = $taskQuery->orderBy('deadline', 'asc')->get();

        $kpi = $employee->calculatePerformanceKpi(
            $filteredTasks, 
            $bulan === 'all' ? now()->month : (int)$bulan, 
            (int)$tahun
        );

        $absensiList = $attendance->recordsFor($employee->id, $bulan, $tahun);
        $attendanceSummary = $attendance->summarize($absensiList, $employee->id, $bulan, $tahun);

        $totalHadir = $attendanceSummary['total_hadir'];
        $tepatWaktu = $attendanceSummary['tepat_waktu'];
        $terlambat = $attendanceSummary['terlambat'];
        $izinCount = $attendanceSummary['izin_hari'];

        return view('manajer.employee_detail', compact(
            'employee', 
            'filteredTasks', 
            'kpi', 
            'bulan', 
            'tahun', 
            'totalHadir', 
            'tepatWaktu', 
            'terlambat', 
            'izinCount'
        ));
    }

    /**
     * Show task evaluation form.
     */
    public function showEvaluate($id)
    {
        $task = Task::with('karyawan', 'progress', 'evaluasi', 'checklists')->findOrFail($id);
        $this->authorize('evaluate', $task);

        if (!in_array($task->status, ['Completed', 'Reviewed', 'Revisi'])) {
            return redirect()->route('manajer.dashboard')
                ->with('error', 'Tugas ini belum diselesaikan oleh karyawan.');
        }

        $autoScore = $task->calculateScore();

        return view('manajer.evaluate', compact('task', 'autoScore'));
    }

    /**
     * Store task evaluation review.
     */
    public function storeEvaluate(Request $request, $id)
    {
        $task = Task::with('checklists')->findOrFail($id);
        $this->authorize('evaluate', $task);

        if (!in_array($task->status, ['Completed', 'Reviewed', 'Revisi'])) {
            return redirect()->route('manajer.dashboard')
                ->with('error', 'Tugas ini belum diselesaikan oleh karyawan.');
        }

        $request->validate([
            'status_evaluasi'           => ['required', 'string', 'in:Sesuai Ekspektasi,Belum Sesuai Ekspektasi,Perlu Revisi'],
            'catatan'                   => ['required', 'string', 'max:2000'],
            'kualitas'                  => ['nullable', 'integer', 'min:0', 'max:100'],
            'komunikasi'                => ['nullable', 'integer', 'min:0', 'max:100'],
            'kerja_sama'                => ['nullable', 'integer', 'min:0', 'max:100'],
            'disiplin_tanggung_jawab'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'problem_solving'           => ['nullable', 'integer', 'min:0', 'max:100'],
            'checklist_revisi'          => ['nullable', 'array'],
            'checklist_revisi.*'        => ['nullable', 'string', 'max:500'],
            'checklist_ditandai'        => ['nullable', 'array'],
            'checklist_ditandai.*'      => ['nullable', 'integer'],
        ]);

        $autoScore = $task->calculateScore();
        $aspectFields = ['kualitas', 'komunikasi', 'kerja_sama', 'disiplin_tanggung_jawab', 'problem_solving'];
        $aspects = [];

        foreach ($aspectFields as $field) {
            $value = $request->input($field);
            $aspects[$field] = is_numeric($value)
                ? max(0, min(100, (int) $value))
                : $autoScore;
        }

        $averageAspectScore = (int) round(array_sum($aspects) / count($aspects));
        $score = match ($request->status_evaluasi) {
            'Perlu Revisi' => max(0, $averageAspectScore - 15),
            'Belum Sesuai Ekspektasi' => max(0, $averageAspectScore - 20),
            default => $averageAspectScore,
        };

        $isRevision = $request->status_evaluasi === 'Perlu Revisi';

        DB::transaction(function () use ($task, $request, $aspects, $score, $isRevision) {
            Evaluasi::updateOrCreate(
                ['task_id' => $task->id],
                [
                    'id_manajer'              => Auth::id(),
                    'status_evaluasi'         => $request->status_evaluasi,
                    'catatan'                 => $request->catatan,
                    'score'                   => $score,
                    'kualitas'                => $aspects['kualitas'],
                    'komunikasi'              => $aspects['komunikasi'],
                    'kerja_sama'              => $aspects['kerja_sama'],
                    'disiplin_tanggung_jawab' => $aspects['disiplin_tanggung_jawab'],
                    'problem_solving'         => $aspects['problem_solving'],
                    'tanggal_evaluasi'        => now(),
                ]
            );

            if ($isRevision) {
                $task->status = 'Revisi';
                $task->save();

                $ditandaiIds = array_map('intval', (array) $request->input('checklist_ditandai', []));
                $catatanRevisi = (array) $request->input('checklist_revisi', []);

                foreach ($task->checklists as $item) {
                    $perluRevisi = in_array($item->id, $ditandaiIds, true);
                    $catatan = trim((string) ($catatanRevisi[$item->id] ?? ''));

                    $item->update([
                        'perlu_revisi'   => $perluRevisi,
                        'catatan_revisi' => $perluRevisi && $catatan !== '' ? $catatan : null,
                        // Poin yang ditandai wajib dikerjakan ulang oleh karyawan.
                        'is_completed'   => $perluRevisi ? false : $item->is_completed,
                    ]);
                }

                $task->forgetComputedAttributes();
                $task->load('checklists');

                $jumlahDitandai = count(array_unique($ditandaiIds));
                $keteranganPoin = $jumlahDitandai > 0
                    ? " ($jumlahDitandai poin checklist perlu diperbaiki)"
                    : '';

                Progress::create([
                    'task_id'        => $task->id,
                    'persentase'     => $task->persentase,
                    'keterangan'     => '⚠️ Manajer meminta perbaikan/revisi' . $keteranganPoin . ': ' . $request->catatan,
                    'tanggal_update' => now(),
                ]);
            } else {
                $task->status = 'Reviewed';
                $task->save();

                // Reset flag revisi, catatan tetap disimpan sebagai histori.
                $task->checklists()->update(['perlu_revisi' => false]);
                $task->forgetComputedAttributes();
                $task->load('checklists');

                Progress::create([
                    'task_id'        => $task->id,
                    'persentase'     => 100,
                    'keterangan'     => 'Evaluasi selesai oleh manajer: ' . $request->status_evaluasi,
                    'tanggal_update' => now(),
                ]);
            }
        });

        if ($isRevision) {
            return redirect()->route('manajer.tasks')
                ->with('warning', 'Evaluasi disimpan dengan status "Perlu Revisi". Tugas dikembalikan ke karyawan untuk diperbaiki.');
        }

        return redirect()->route('manajer.tasks')
            ->with('success', 'Evaluasi tugas berhasil disimpan.');
    }

    /**
     * List all evaluations made by the manager.
     */
    public function evaluations()
    {
        $evaluations = Evaluasi::with(['task.karyawan'])
            ->where('id_manajer', Auth::id())
            ->orderBy('tanggal_evaluasi', 'desc')
            ->get();

        return view('manajer.evaluations', compact('evaluations'));
    }
}
