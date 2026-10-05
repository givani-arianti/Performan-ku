<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Task;
use App\Models\Evaluasi;
use App\Models\TaskChecklist;
use Illuminate\Support\Facades\Auth;

class ManajerController extends Controller
{
    /**
     * Display Manager dashboard.
     */
    public function dashboard()
    {
        // Tasks waiting for evaluation (Completed tasks not reviewed yet)
        $pendingEvaluations = Task::where('status', 'Completed')
            ->with(['karyawan', 'progress'])
            ->orderBy('updated_at', 'asc')
            ->get();

        // Stats
        $employees = User::where('role', 'karyawan')->get();
        $totalEmployees = $employees->count();
        
        $tasks = Task::with('evaluasi')->get();
        $totalTasks = $tasks->count();
        $reviewedTasks = $tasks->where('status', 'Reviewed')->count();

        $averageScore = $employees->map(fn ($employee) => $employee->calculatePerformanceKpi()['final_score'])->avg() ?? 0;

        // Get employee list with scores for leaderboard
        $employeeScores = $employees->map(function($emp) {
            $tasks = $emp->assignedTasks;
            $avg = $emp->calculatePerformanceKpi($tasks)['final_score'];
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
            'averageScore',
            'employeeScores'
        ));
    }

    // =============================================
    // TASK MANAGEMENT (Manajer mengelola tugas)
    // =============================================

    /**
     * List all tasks with filter options.
     */
    public function tasks(Request $request)
    {
        $query = Task::with(['karyawan', 'hr', 'progress', 'checklists'])->orderBy('created_at', 'desc');

        if ($request->filled('karyawan_id')) {
            $query->where('id_karyawan', $request->karyawan_id);
        }

        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        $allTasks = $query->get();

        $activeTasks = $allTasks->whereIn('status', ['Pending', 'In Progress']);
        $completedTasks = $allTasks->whereIn('status', ['Completed', 'Reviewed']);

        $employees = User::where('role', 'karyawan')->orderBy('name', 'asc')->get();
        $divisiList = Task::whereNotNull('divisi')->pluck('divisi')->unique();

        return view('manajer.tasks', compact('activeTasks', 'completedTasks', 'employees', 'divisiList'));
    }

    /**
     * Store new task.
     */
    public function storeTask(Request $request)
    {
        $request->validate([
            'judul_tugas' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'target' => ['required', 'string', 'max:255'],
            'divisi' => ['nullable', 'string', 'max:100'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'id_karyawan' => ['required', 'exists:users,id'],
            'checklists' => ['nullable', 'array'],
            'checklists.*' => ['nullable', 'string', 'max:255'],
        ]);

        $task = Task::create([
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'target' => $request->target,
            'divisi' => $request->divisi,
            'deadline' => $request->deadline,
            'id_karyawan' => $request->id_karyawan,
            'id_hr' => Auth::id(),
            'status' => 'Pending',
        ]);

        if ($request->has('checklists') && is_array($request->checklists)) {
            foreach ($request->checklists as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    TaskChecklist::create([
                        'task_id' => $task->id,
                        'judul' => $item,
                        'is_completed' => false,
                    ]);
                }
            }
        }

        return redirect()->route('manajer.tasks')
            ->with('success', 'Tugas dan checklist berhasil dibuat dan ditugaskan kepada karyawan.');
    }

    /**
     * Update task.
     */
    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'judul_tugas' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'target' => ['required', 'string', 'max:255'],
            'divisi' => ['nullable', 'string', 'max:100'],
            'deadline' => ['required', 'date'],
            'id_karyawan' => ['required', 'exists:users,id'],
            'status' => ['required', 'string', 'in:Pending,In Progress,Completed,Reviewed'],
            'checklists' => ['nullable', 'array'],
            'checklists.*' => ['nullable', 'string', 'max:255'],
        ]);

        $task->update([
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'target' => $request->target,
            'divisi' => $request->divisi,
            'deadline' => $request->deadline,
            'id_karyawan' => $request->id_karyawan,
            'status' => $request->status,
        ]);

        $newChecklists = array_filter(array_map('trim', $request->checklists ?? []));
        
        $existingItems = $task->checklists;
        foreach ($existingItems as $existing) {
            if (!in_array($existing->judul, $newChecklists)) {
                $existing->delete();
            }
        }

        $existingTitles = $task->checklists()->pluck('judul')->toArray();
        foreach ($newChecklists as $title) {
            if (!in_array($title, $existingTitles)) {
                TaskChecklist::create([
                    'task_id' => $task->id,
                    'judul' => $title,
                    'is_completed' => false,
                ]);
            }
        }

        return redirect()->route('manajer.tasks')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Delete task.
     */
    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('manajer.tasks')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    // =============================================
    // PERFORMANCE & EVALUATION
    // =============================================

    /**
     * List employee performance data.
     */
    public function performance(Request $request)
    {
        $selectedDivisi = $request->input('divisi');

        $divisiList = User::whereNotNull('divisi')
            ->where('divisi', '!=', '')
            ->pluck('divisi')
            ->unique()
            ->values();

        $query = User::where('role', 'karyawan')
            ->with(['assignedTasks.evaluasi', 'assignedTasks.progress']);

        if ($selectedDivisi) {
            $query->where('divisi', $selectedDivisi);
        }

        $employees = $query->get();

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

        return view('manajer.performance', compact('employeeData', 'divisiList', 'selectedDivisi'));
    }

    /**
     * Detail view of single employee performance.
     */
    public function employeeDetail($id)
    {
        $employee = User::where('role', 'karyawan')
            ->with(['assignedTasks' => function($q) {
                $q->orderBy('deadline', 'asc');
            }, 'assignedTasks.evaluasi', 'assignedTasks.progress'])
            ->findOrFail($id);

        return view('manajer.employee_detail', compact('employee'));
    }

    /**
     * Show task evaluation form.
     */
    public function showEvaluate($id)
    {
        $task = Task::with('karyawan', 'progress', 'evaluasi')->findOrFail($id);

        if (!in_array($task->status, ['Completed', 'Reviewed'])) {
            return redirect()->route('manajer.dashboard')
                ->with('error', 'Tugas ini belum diselesaikan oleh karyawan.');
        }

        $autoScore = $task->calculateScore();

        return view('manajer.evaluate', compact('task', 'autoScore'));
    }

    /**
     * Store task evaluation.
     */
    public function storeEvaluate(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        if (!in_array($task->status, ['Completed', 'Reviewed'])) {
            return redirect()->route('manajer.dashboard')
                ->with('error', 'Tugas ini belum diselesaikan oleh karyawan.');
        }

        $request->validate([
            'status_evaluasi' => ['required', 'string', 'in:Sesuai Ekspektasi,Belum Sesuai Ekspektasi'],
            'catatan' => ['required', 'string', 'max:2000'],
            'kualitas' => ['required', 'integer', 'in:40,60,80,100'],
            'komunikasi' => ['required', 'integer', 'min:0', 'max:100'],
            'kerja_sama' => ['required', 'integer', 'min:0', 'max:100'],
            'disiplin_tanggung_jawab' => ['required', 'integer', 'min:0', 'max:100'],
            'problem_solving' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        Evaluasi::updateOrCreate(
            ['task_id' => $task->id],
            [
                'id_manajer' => Auth::id(),
                'status_evaluasi' => $request->status_evaluasi,
                'catatan' => $request->catatan,
                'score' => $request->kualitas,
                'kualitas' => $request->kualitas,
                'komunikasi' => $request->komunikasi,
                'kerja_sama' => $request->kerja_sama,
                'disiplin_tanggung_jawab' => $request->disiplin_tanggung_jawab,
                'problem_solving' => $request->problem_solving,
                'tanggal_evaluasi' => now(),
            ]
        );

        $task->status = 'Reviewed';
        $task->save();

        return redirect()->route('manajer.dashboard')
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
