<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_must_complete_all_checklists_before_submitting_work(): void
    {
        Storage::fake('public');

        [$employee, $task] = $this->makeTaskWithChecklist(false);

        $this->actingAs($employee)
            ->post("/karyawan/tasks/{$task->id}/submit", [
                'dokumen' => UploadedFile::fake()->create('hasil.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect("/karyawan/tasks/{$task->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'In Progress']);
        Storage::disk('public')->assertDirectoryEmpty('hasil-tugas');
    }

    public function test_employee_can_submit_after_completing_checklists_and_cannot_resubmit_final_task(): void
    {
        Storage::fake('public');

        [$employee, $task] = $this->makeTaskWithChecklist(true);

        $this->actingAs($employee)
            ->post("/karyawan/tasks/{$task->id}/submit", [
                'dokumen' => UploadedFile::fake()->create('hasil.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect("/karyawan/tasks/{$task->id}")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Completed',
            'hasil_kerja' => 'hasil.pdf',
        ]);

        $this->assertSame(100, $task->fresh()->persentase);

        $this->actingAs($employee)
            ->post("/karyawan/tasks/{$task->id}/submit", [
                'dokumen' => UploadedFile::fake()->create('hasil-lagi.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect("/karyawan/tasks/{$task->id}")
            ->assertSessionHas('error');
    }

    public function test_checklist_cannot_be_changed_after_task_is_reviewed(): void
    {
        [$employee, $task] = $this->makeTaskWithChecklist(true);
        $task->update(['status' => 'Reviewed']);
        $checklist = $task->checklists->first();

        $this->actingAs($employee)
            ->post("/karyawan/tasks/{$task->id}/checklist/{$checklist->id}/toggle")
            ->assertRedirect("/karyawan/tasks/{$task->id}")
            ->assertSessionHas('error');

        $this->assertTrue($checklist->fresh()->is_completed);
    }

    private function makeTaskWithChecklist(bool $completed): array
    {
        $employee = User::factory()->create([
            'role' => 'karyawan',
            'divisi' => 'IT',
        ]);

        $task = Task::create([
            'judul_tugas' => 'Tugas Pengujian',
            'deskripsi' => 'Deskripsi tugas pengujian.',
            'target' => '100%',
            'divisi' => 'IT',
            'deadline' => now()->addDays(3)->toDateString(),
            'id_hr' => $employee->id,
            'id_karyawan' => $employee->id,
            'status' => 'In Progress',
        ]);

        TaskChecklist::create([
            'task_id' => $task->id,
            'judul' => 'Poin penting',
            'is_completed' => $completed,
        ]);

        return [$employee, $task->fresh()];
    }
}
