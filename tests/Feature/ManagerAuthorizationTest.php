<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_cannot_manage_tasks_outside_their_division(): void
    {
        $manager = User::factory()->create([
            'role' => 'manajer',
            'divisi' => 'IT & Software',
        ]);
        $marketingEmployee = User::factory()->create([
            'role' => 'karyawan',
            'divisi' => 'Marketing & Sales',
        ]);
        $task = $this->makeTask($manager, $marketingEmployee, [
            'divisi' => 'Marketing & Sales',
            'status' => 'Completed',
        ]);

        $this->actingAs($manager)
            ->get("/manajer/tasks/{$task->id}/evaluate")
            ->assertForbidden();

        $this->actingAs($manager)
            ->get("/manajer/performance/employee/{$marketingEmployee->id}")
            ->assertForbidden();

        $this->actingAs($manager)
            ->put("/manajer/tasks/{$task->id}", $this->taskPayload($marketingEmployee))
            ->assertForbidden();

        $this->actingAs($manager)
            ->delete("/manajer/tasks/{$task->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_manager_can_evaluate_tasks_in_their_division(): void
    {
        $manager = User::factory()->create([
            'role' => 'manajer',
            'divisi' => 'IT & Software',
        ]);
        $employee = User::factory()->create([
            'role' => 'karyawan',
            'divisi' => 'IT & Software',
        ]);
        $task = $this->makeTask($manager, $employee, [
            'divisi' => 'IT & Software',
            'status' => 'Completed',
        ]);

        $this->actingAs($manager)
            ->get("/manajer/tasks/{$task->id}/evaluate")
            ->assertOk();

        $this->actingAs($manager)
            ->get("/manajer/performance/employee/{$employee->id}")
            ->assertOk();
    }

    public function test_manager_cannot_assign_task_to_employee_from_another_division(): void
    {
        $manager = User::factory()->create([
            'role' => 'manajer',
            'divisi' => 'IT & Software',
        ]);
        $marketingEmployee = User::factory()->create([
            'role' => 'karyawan',
            'divisi' => 'Marketing & Sales',
        ]);

        $this->actingAs($manager)
            ->post('/manajer/tasks', $this->taskPayload($marketingEmployee))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('tasks', 0);
    }

    private function makeTask(User $manager, User $employee, array $overrides = []): Task
    {
        return Task::create(array_merge([
            'judul_tugas' => 'Tugas Pengujian',
            'deskripsi' => 'Deskripsi tugas pengujian.',
            'target' => '100%',
            'divisi' => $employee->divisi,
            'deadline' => now()->addDays(3)->toDateString(),
            'id_hr' => $manager->id,
            'id_karyawan' => $employee->id,
            'status' => 'Pending',
        ], $overrides));
    }

    private function taskPayload(User $employee): array
    {
        return [
            'judul_tugas' => 'Tugas Pengujian',
            'deskripsi' => 'Deskripsi tugas pengujian.',
            'target' => '100%',
            'divisi' => $employee->divisi,
            'deadline' => now()->addDays(3)->toDateString(),
            'id_karyawan' => $employee->id,
            'status' => 'Pending',
            'checklists' => ['Poin 1'],
        ];
    }
}
