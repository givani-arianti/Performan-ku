<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PengajuanIzin;
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
}
