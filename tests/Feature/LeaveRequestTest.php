<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\PengajuanIzin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_submit_leave_request()
    {
        $user = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($user)->post('/karyawan/absensi/pengajuan', [
            'jenis' => 'cuti',
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-03',
            'alasan' => 'Cuti keperluan keluarga',
        ]);

        $response->assertRedirect('/karyawan/absensi');

        $this->assertDatabaseHas('pengajuan_izins', [
            'user_id' => $user->id,
            'jenis' => 'cuti',
            'alasan' => 'Cuti keperluan keluarga',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_leave_request()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $karyawan = User::factory()->create(['role' => 'karyawan']);

        $izin = PengajuanIzin::create([
            'user_id' => $karyawan->id,
            'jenis' => 'sakit',
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-01',
            'alasan' => 'Demam tinggi',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/absensi/pengajuan/{$izin->id}/approve");

        $response->assertRedirect();

        $this->assertDatabaseHas('pengajuan_izins', [
            'id' => $izin->id,
            'status' => 'approved',
            'disetujui_oleh' => $admin->id,
        ]);

        $this->assertDatabaseHas('absensis', [
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-01',
            'status' => 'Sakit',
        ]);
    }

    public function test_approving_leave_keeps_real_attendance_and_skips_weekends(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $karyawan = User::factory()->create(['role' => 'karyawan']);

        Absensi::create([
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-02',
            'jenis' => 'masuk',
            'jam' => '08:45:00',
            'status' => 'Terlambat',
            'detail' => 'Terlambat 15 menit',
        ]);

        $izin = PengajuanIzin::create([
            'user_id' => $karyawan->id,
            'jenis' => 'cuti',
            'tanggal_mulai' => '2026-10-02',
            'tanggal_selesai' => '2026-10-05',
            'alasan' => 'Cuti keluarga',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post("/admin/absensi/pengajuan/{$izin->id}/approve")
            ->assertRedirect();

        // The real check-in is preserved instead of being overwritten by 08:00.
        $this->assertDatabaseHas('absensis', [
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-02',
            'jenis' => 'masuk',
            'jam' => '08:45:00',
            'status' => 'Terlambat',
        ]);

        // Weekend dates are skipped, Monday is added as approved leave.
        $this->assertDatabaseMissing('absensis', [
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-03',
        ]);
        $this->assertDatabaseMissing('absensis', [
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-04',
        ]);
        $this->assertDatabaseHas('absensis', [
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-05',
            'jenis' => 'masuk',
            'status' => 'Cuti',
        ]);

        // Processing the same request twice must not create extra records.
        $this->actingAs($admin)
            ->post("/admin/absensi/pengajuan/{$izin->id}/approve")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('absensis', 2);
    }
}
