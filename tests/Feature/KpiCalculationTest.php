<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Task;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_kpi_does_not_fall_back_to_tasks_from_other_months(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);

        Task::create([
            'judul_tugas' => 'Tugas Bulan Lalu',
            'deskripsi' => 'Tugas lama.',
            'target' => '100%',
            'divisi' => 'IT',
            'deadline' => now()->subMonthNoOverflow()->toDateString(),
            'id_hr' => $employee->id,
            'id_karyawan' => $employee->id,
            'status' => 'Reviewed',
        ]);

        $kpi = $employee->calculatePerformanceKpi(null, now()->month, now()->year);

        $this->assertSame(0, $kpi['total_tasks']);
        $this->assertSame(0, $kpi['final_score']);
        $this->assertSame('Belum Ada Tugas', $kpi['category']);
    }

    public function test_unfinished_task_does_not_earn_timeliness_or_speed_points(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);

        $task = Task::create([
            'judul_tugas' => 'Tugas Sedang Berjalan',
            'deskripsi' => 'Belum selesai.',
            'target' => '100%',
            'divisi' => 'IT',
            'deadline' => now()->addDays(5)->toDateString(),
            'id_hr' => $employee->id,
            'id_karyawan' => $employee->id,
            'status' => 'In Progress',
        ]);

        $task->progress()->create([
            'persentase' => 50,
            'keterangan' => 'Baru setengah jalan.',
            'tanggal_update' => now(),
        ]);

        $kpi = $employee->calculatePerformanceKpi(collect([$task]), now()->month, now()->year);

        $this->assertSame(50.0, (float) $kpi['target_score']);
        $this->assertSame(0.0, (float) $kpi['timeliness_score']);
        $this->assertSame(0.0, (float) $kpi['speed_score']);
        $this->assertSame(20.0, (float) $kpi['final_score']);
    }

    public function test_attendance_summary_does_not_count_approved_leave_as_present(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);

        Absensi::create([
            'user_id' => $employee->id,
            'tanggal' => now()->toDateString(),
            'jenis' => 'masuk',
            'jam' => '08:00:00',
            'status' => 'Cuti',
            'detail' => 'Disetujui: cuti',
        ]);

        $service = app(AttendanceService::class);
        $records = $service->recordsFor($employee->id, now()->format('m'), now()->format('Y'));
        $summary = $service->summarize($records, $employee->id, now()->format('m'), now()->format('Y'));

        $this->assertSame(0, $summary['total_hadir']);
        $this->assertSame(0, $summary['tepat_waktu']);
        $this->assertSame(0, $summary['terlambat']);
    }
}
