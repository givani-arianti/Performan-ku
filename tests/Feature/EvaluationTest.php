<?php

namespace Tests\Feature;

use App\Models\Evaluasi;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_evaluation_uses_aspect_scores_and_penalizes_below_expectation(): void
    {
        [$manager, $employee, $task] = $this->makeEvaluationContext();

        $this->actingAs($manager)
            ->post("/manajer/tasks/{$task->id}/evaluate", [
                'status_evaluasi' => 'Belum Sesuai Ekspektasi',
                'catatan' => 'Perlu peningkatan kualitas dan problem solving.',
                'kualitas' => 90,
                'komunikasi' => 80,
                'kerja_sama' => 70,
                'disiplin_tanggung_jawab' => 60,
                'problem_solving' => 50,
            ])
            ->assertRedirect('/manajer/tasks')
            ->assertSessionHas('success');

        // Average of aspects = 70, minus 20 for "Belum Sesuai Ekspektasi".
        $this->assertDatabaseHas('evaluasi', [
            'task_id' => $task->id,
            'score' => 50,
            'kualitas' => 90,
            'komunikasi' => 80,
            'kerja_sama' => 70,
            'disiplin_tanggung_jawab' => 60,
            'problem_solving' => 50,
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Reviewed',
        ]);
    }

    public function test_revision_flags_checklist_items_and_syncs_task_progress(): void
    {
        [$manager, $employee, $task] = $this->makeEvaluationContext();

        $first = TaskChecklist::create([
            'task_id' => $task->id,
            'judul' => 'Poin satu',
            'is_completed' => true,
        ]);
        $second = TaskChecklist::create([
            'task_id' => $task->id,
            'judul' => 'Poin dua',
            'is_completed' => true,
        ]);

        $this->actingAs($manager)
            ->post("/manajer/tasks/{$task->id}/evaluate", [
                'status_evaluasi' => 'Perlu Revisi',
                'catatan' => 'Poin dua belum sesuai instruksi.',
                'checklist_ditandai' => [$second->id],
                'checklist_revisi' => [$second->id => 'Lengkapi bagian akhir dokumen.'],
                'kualitas' => 90,
                'komunikasi' => 90,
                'kerja_sama' => 90,
                'disiplin_tanggung_jawab' => 90,
                'problem_solving' => 90,
            ])
            ->assertRedirect('/manajer/tasks')
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('evaluasi', [
            'task_id' => $task->id,
            'score' => 75,
            'status_evaluasi' => 'Perlu Revisi',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Revisi',
        ]);

        $this->assertDatabaseHas('task_checklists', [
            'id' => $second->id,
            'is_completed' => false,
            'perlu_revisi' => true,
            'catatan_revisi' => 'Lengkapi bagian akhir dokumen.',
        ]);

        $this->assertDatabaseHas('task_checklists', [
            'id' => $first->id,
            'is_completed' => true,
            'perlu_revisi' => false,
        ]);

        $this->assertSame(50, $task->fresh()->persentase);
        $this->assertDatabaseHas('progress', [
            'task_id' => $task->id,
            'persentase' => 50,
        ]);
    }

    public function test_aspect_scores_are_optional_and_fall_back_to_auto_score(): void
    {
        [$manager, $employee, $task] = $this->makeEvaluationContext();

        $autoScore = $task->calculateScore();

        $this->actingAs($manager)
            ->post("/manajer/tasks/{$task->id}/evaluate", [
                'status_evaluasi' => 'Sesuai Ekspektasi',
                'catatan' => 'Hasil kerja sudah sesuai target.',
            ])
            ->assertRedirect('/manajer/tasks')
            ->assertSessionHas('success');

        $evaluasi = Evaluasi::where('task_id', $task->id)->firstOrFail();

        $this->assertSame($autoScore, (int) $evaluasi->score);
        $this->assertSame($autoScore, (int) $evaluasi->kualitas);
        $this->assertSame($autoScore, (int) $evaluasi->problem_solving);
    }

    private function makeEvaluationContext(): array
    {
        $manager = User::factory()->create([
            'role' => 'manajer',
            'divisi' => 'IT',
        ]);
        $employee = User::factory()->create([
            'role' => 'karyawan',
            'divisi' => 'IT',
        ]);
        $task = Task::create([
            'judul_tugas' => 'Tugas Evaluasi',
            'deskripsi' => 'Deskripsi tugas evaluasi.',
            'target' => '100%',
            'divisi' => 'IT',
            'deadline' => now()->addDays(2)->toDateString(),
            'id_hr' => $manager->id,
            'id_karyawan' => $employee->id,
            'status' => 'Completed',
        ]);

        return [$manager, $employee, $task];
    }
}
