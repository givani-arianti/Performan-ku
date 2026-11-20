<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/monitoring')->assertOk();
        $this->actingAs($admin)->get('/admin/absensi')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }

    public function test_manager_pages_render(): void
    {
        $manager = User::where('email', 'manajer@gmail.com')->firstOrFail();
        $employee = User::where('email', 'karyawan@gmail.com')->firstOrFail();
        $task = Task::where('id_karyawan', $employee->id)->firstOrFail();

        $this->actingAs($manager)->get('/manajer/dashboard')->assertOk();
        $this->actingAs($manager)->get('/manajer/tasks')->assertOk();
        $this->actingAs($manager)->get('/manajer/performance')->assertOk();
        $this->actingAs($manager)->get('/manajer/evaluations')->assertOk();
        $this->actingAs($manager)->get("/manajer/performance/employee/{$employee->id}")->assertOk();
        $this->actingAs($manager)->get("/manajer/tasks/{$task->id}/evaluate")->assertOk();
    }

    public function test_employee_pages_render(): void
    {
        $employee = User::where('email', 'karyawan@gmail.com')->firstOrFail();
        $task = Task::where('id_karyawan', $employee->id)->firstOrFail();

        $this->actingAs($employee)->get('/karyawan/dashboard')->assertOk();
        $this->actingAs($employee)->get('/karyawan/tasks')->assertOk();
        $this->actingAs($employee)->get('/karyawan/laporan')->assertOk();
        $this->actingAs($employee)->get('/karyawan/absensi')->assertOk();
        $this->actingAs($employee)->get("/karyawan/tasks/{$task->id}")->assertOk();
    }
}
