<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendancePersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_attendance_is_saved_to_database_with_late_status()
    {
        $user = User::factory()->create([
            'role' => 'karyawan',
        ]);

        $this->actingAs($user);

        Carbon::setTestNow(Carbon::parse('2026-09-01 09:06:00'));

        $this->post('/karyawan/absensi', ['jenis' => 'masuk'])
            ->assertRedirect('/karyawan/absensi');

        $this->assertDatabaseHas('absensis', [
            'user_id' => $user->id,
            'tanggal' => '2026-09-01',
            'jenis' => 'masuk',
            'status' => 'Terlambat',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-09-01 17:10:00'));

        $this->post('/karyawan/absensi', ['jenis' => 'pulang'])
            ->assertRedirect('/karyawan/absensi');

        $this->assertDatabaseHas('absensis', [
            'user_id' => $user->id,
            'tanggal' => '2026-09-01',
            'jenis' => 'pulang',
        ]);

        Carbon::setTestNow();
    }
}
