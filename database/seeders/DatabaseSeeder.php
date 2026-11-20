<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\Progress;
use App\Models\Evaluasi;
use App\Models\Absensi;
use App\Models\PengajuanIzin;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic enterprise data.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin HR',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'divisi' => 'Human Resources',
            ]
        );

        $manajer = User::updateOrCreate(
            ['email' => 'manajer@gmail.com'],
            [
                'name' => 'Bapak Manajer',
                'password' => Hash::make('password123'),
                'role' => 'manajer',
                'divisi' => 'IT & Software',
            ]
        );

        $karyawan1 = User::updateOrCreate(
            ['email' => 'karyawan@gmail.com'],
            [
                'name' => 'Pegawai Satu',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'divisi' => 'IT & Software',
            ]
        );

        $karyawan2 = User::updateOrCreate(
            ['email' => 'karyawan2@gmail.com'],
            [
                'name' => 'Pegawai Dua',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'divisi' => 'Marketing & Sales',
            ]
        );

        // 2. Clear old tasks & related tables to prevent duplicate seeding
        Task::query()->delete();
        Absensi::query()->delete();
        PengajuanIzin::query()->delete();

        // 3. SEED TASKS FOR PEGAWAI SATU (IT & Software)
        // Task 1: November 2026 - Completed & Evaluated (Sesuai Ekspektasi)
        $t1 = Task::create([
            'judul_tugas' => 'Implementasi Modul Autentikasi OAuth2 & JWT Security',
            'deskripsi' => "Membangun sistem autentikasi multi-tenant yang aman menggunakan protokol OAuth2 dan JWT Bearer Token.\nInstruksi:\n1. Terapkan refresh token rotation.\n2. Buat middleware validasi hak akses per divisi.\n3. Implementasi rate limiter untuk mencegah brute-force login.",
            'target' => 'Sistem login aman 100% lulus security audit internal',
            'divisi' => 'IT & Software',
            'deadline' => '2026-11-04',
            'id_hr' => $manajer->id,
            'id_karyawan' => $karyawan1->id,
            'status' => 'Reviewed',
            'hasil_kerja' => 'Dokumentasi_JWT_OAuth2_Security.pdf',
            'created_at' => Carbon::parse('2026-10-28 08:30:00'),
            'updated_at' => Carbon::parse('2026-11-03 16:45:00'),
        ]);

        TaskChecklist::create(['task_id' => $t1->id, 'judul' => 'Setup JWT library & asymmetric RSA keys', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t1->id, 'judul' => 'Pembuatan route login, refresh token, & revoke', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t1->id, 'judul' => 'Middleware Role-based Access Control (RBAC)', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t1->id, 'judul' => 'Unit testing autentikasi & Postman collection', 'is_completed' => true]);

        Progress::create([
            'task_id' => $t1->id,
            'persentase' => 100,
            'keterangan' => 'Modul autentikasi selesai diimplementasikan 1 hari sebelum deadline. Lolos pengujian Postman.',
            'tanggal_update' => Carbon::parse('2026-11-03 16:40:00'),
        ]);

        Evaluasi::create([
            'task_id' => $t1->id,
            'id_manajer' => $manajer->id,
            'status_evaluasi' => 'Sesuai Ekspektasi',
            'catatan' => 'Implementasi token refresh dan middleware guard sangat rapi. Penanganan exception dan validasi request lengkap sesuai standar clean code. Kerja luar biasa!',
            'score' => 95,
            'kualitas' => 95,
            'komunikasi' => 90,
            'kerja_sama' => 95,
            'disiplin_tanggung_jawab' => 95,
            'problem_solving' => 95,
            'tanggal_evaluasi' => Carbon::parse('2026-11-04 10:00:00'),
        ]);

        // Task 2: November 2026 - Completed & Evaluated (Sesuai Ekspektasi)
        $t2 = Task::create([
            'judul_tugas' => 'Integrasi Payment Gateway Midtrans Snap API & Webhook',
            'deskripsi' => "Menghubungkan aplikasi Performan-Ku dengan sistem pembayaran digital Midtrans untuk langganan korporat.\nTarget luaran:\n1. Support Virtual Account (BCA, Mandiri, BNI, BRI) & QRIS.\n2. Endpoint webhook otomatis untuk verifikasi status transaksi realtime.\n3. Penanganan signature key verification.",
            'target' => 'Transaksi pembayaran realtime otomatis terverifikasi',
            'divisi' => 'IT & Software',
            'deadline' => '2026-11-06',
            'id_hr' => $manajer->id,
            'id_karyawan' => $karyawan1->id,
            'status' => 'Reviewed',
            'hasil_kerja' => 'Laporan_Integrasi_Midtrans_Payment.pdf',
            'created_at' => Carbon::parse('2026-11-01 09:00:00'),
            'updated_at' => Carbon::parse('2026-11-06 14:15:00'),
        ]);

        TaskChecklist::create(['task_id' => $t2->id, 'judul' => 'Integrasi Midtrans Snap SDK PHP', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t2->id, 'judul' => 'Pembuatan handler webhook order status', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t2->id, 'judul' => 'Pengujian transaksi Sandbox Virtual Account', 'is_completed' => true]);

        Progress::create([
            'task_id' => $t2->id,
            'persentase' => 100,
            'keterangan' => 'Selesai tepat pada hari batas waktu. Transaksi sandbox berhasil terverifikasi otomatis via webhook.',
            'tanggal_update' => Carbon::parse('2026-11-06 14:10:00'),
        ]);

        Evaluasi::create([
            'task_id' => $t2->id,
            'id_manajer' => $manajer->id,
            'status_evaluasi' => 'Sesuai Ekspektasi',
            'catatan' => 'Webhook response handled dengan aman menggunakan verifikasi hash signature. Alur status transaksi sudah sangat baik dan siap rilis ke production.',
            'score' => 92,
            'kualitas' => 92,
            'komunikasi' => 90,
            'kerja_sama' => 90,
            'disiplin_tanggung_jawab' => 95,
            'problem_solving' => 92,
            'tanggal_evaluasi' => Carbon::parse('2026-11-07 09:30:00'),
        ]);

        // Task 3: November 2026 - REVISI (Perlu Revisi Manajer)
        $t3 = Task::create([
            'judul_tugas' => 'Refactoring Query & Optimasi Indexing Database MySQL',
            'deskripsi' => "Melakukan profiling database dan optimasi query lambat pada dashboard analitik performa karyawan.\nLangkah:\n1. Analisis query log slow queries.\n2. Tambahkan composite index pada kolom filter (tanggal, divisi, status).\n3. Eliminasi N+1 problem pada relasi evaluasi.",
            'target' => 'Response time query analitik dashboard di bawah 150ms',
            'divisi' => 'IT & Software',
            'deadline' => '2026-11-12',
            'id_hr' => $manajer->id,
            'id_karyawan' => $karyawan1->id,
            'status' => 'Revisi',
            'hasil_kerja' => 'Hasil_Optimasi_Query_Index_Draft1.pdf',
            'created_at' => Carbon::parse('2026-11-03 10:00:00'),
            'updated_at' => Carbon::parse('2026-11-08 11:20:00'),
        ]);

        TaskChecklist::create(['task_id' => $t3->id, 'judul' => 'Analisis slow query log & EXPLAIN statement', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t3->id, 'judul' => 'Pemasangan composite indexing tabel tasks & evaluasi', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t3->id, 'judul' => 'Penerapan Eager Loading (with) untuk relasi Eloquent', 'is_completed' => false]);
        TaskChecklist::create(['task_id' => $t3->id, 'judul' => 'Benchmarking kecepatan load halaman dashboard', 'is_completed' => false]);

        Progress::create([
            'task_id' => $t3->id,
            'persentase' => 80,
            'keterangan' => '⚠️ Manajer meminta perbaikan/revisi: Masih ditemukan potensi N+1 query pada pemanggilan riwayat evaluasi di halaman monitoring. Tolong tambahkan eager loading relasi evaluasi.manajer.',
            'tanggal_update' => Carbon::parse('2026-11-08 11:15:00'),
        ]);

        Evaluasi::create([
            'task_id' => $t3->id,
            'id_manajer' => $manajer->id,
            'status_evaluasi' => 'Perlu Revisi',
            'catatan' => 'Indeks database sudah tepat dan response time menurun drastis. Namun pada controller masih terdeteksi N+1 problem saat looping data evaluasi karyawan. Silakan perbaiki dengan menambahkan eager loading (with) sebelum diajukan kembali.',
            'score' => 78,
            'kualitas' => 78,
            'komunikasi' => 85,
            'kerja_sama' => 80,
            'disiplin_tanggung_jawab' => 80,
            'problem_solving' => 75,
            'tanggal_evaluasi' => Carbon::parse('2026-11-08 11:20:00'),
        ]);

        // Task 4: November 2026 - In Progress (75%)
        $t4 = Task::create([
            'judul_tugas' => 'Pembuatan Dokumentasi RESTful API dengan Swagger OpenAPI 3.0',
            'deskripsi' => 'Menyusun dokumentasi interaktif seluruh endpoint API aplikasi menggunakan format OpenAPI 3.0 dan Swagger UI.',
            'target' => 'Dokumentasi interaktif dapat diuji coba langsung oleh tim frontend',
            'divisi' => 'IT & Software',
            'deadline' => '2026-11-15',
            'id_hr' => $manajer->id,
            'id_karyawan' => $karyawan1->id,
            'status' => 'In Progress',
            'created_at' => Carbon::parse('2026-11-05 13:00:00'),
            'updated_at' => Carbon::parse('2026-11-08 14:00:00'),
        ]);

        TaskChecklist::create(['task_id' => $t4->id, 'judul' => 'Anotasi Swagger controller authentication', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t4->id, 'judul' => 'Anotasi Swagger controller tasks & progress', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t4->id, 'judul' => 'Anotasi Swagger controller absensi & kpi', 'is_completed' => false]);
        TaskChecklist::create(['task_id' => $t4->id, 'judul' => 'Deploy Swagger UI ke endpoint /api/documentation', 'is_completed' => false]);

        Progress::create([
            'task_id' => $t4->id,
            'persentase' => 50,
            'keterangan' => 'Dokumentasi untuk endpoint login dan tasks telah selesai ditulis.',
            'tanggal_update' => Carbon::parse('2026-11-08 14:00:00'),
        ]);

        // Task 5: Data Bulan Januari 2026 (Agar periode Januari 2026 juga langsung terisi riil!)
        $t5 = Task::create([
            'judul_tugas' => 'Penyusunan Arsitektur Microservices & CI/CD Pipeline GitHub Actions',
            'deskripsi' => "Merancang arsitektur deployment otomatis menggunakan Docker container dan GitHub Actions pipeline.\nTarget:\n- Automated testing sebelum push staging.\n- Build image otomatis ke container registry.",
            'target' => 'Proses deployment staging otomatis zero-downtime',
            'divisi' => 'IT & Software',
            'deadline' => '2026-01-25',
            'id_hr' => $manajer->id,
            'id_karyawan' => $karyawan1->id,
            'status' => 'Reviewed',
            'hasil_kerja' => 'Dokumentasi_Architecture_CICD_Pipeline.pdf',
            'created_at' => Carbon::parse('2026-01-10 09:00:00'),
            'updated_at' => Carbon::parse('2026-01-23 15:30:00'),
        ]);

        TaskChecklist::create(['task_id' => $t5->id, 'judul' => 'Pembuatan Dockerfile & docker-compose.yml', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t5->id, 'judul' => 'Workflow GitHub Actions untuk test PHPUnit & Lint', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t5->id, 'judul' => 'Setup SSH Deploy key ke server staging', 'is_completed' => true]);

        Progress::create([
            'task_id' => $t5->id,
            'persentase' => 100,
            'keterangan' => 'CI/CD pipeline aktif dan sukses menjalankan testing sebelum release staging.',
            'tanggal_update' => Carbon::parse('2026-01-23 15:20:00'),
        ]);

        Evaluasi::create([
            'task_id' => $t5->id,
            'id_manajer' => $manajer->id,
            'status_evaluasi' => 'Sesuai Ekspektasi',
            'catatan' => 'Pipeline GitHub Actions berhasil deploy ke staging server dengan zero downtime. Pengurangan waktu deploy mencapai 70%. Hasil memuaskan!',
            'score' => 96,
            'kualitas' => 96,
            'komunikasi' => 95,
            'kerja_sama' => 95,
            'disiplin_tanggung_jawab' => 98,
            'problem_solving' => 96,
            'tanggal_evaluasi' => Carbon::parse('2026-01-24 11:00:00'),
        ]);

        // Task 6: Data Bulan Januari 2026 (Kedua)
        $t6 = Task::create([
            'judul_tugas' => 'Audit Keamanan Sistem & Penetrasi Testing OWASP Top 10',
            'deskripsi' => "Melakukan security audit terhadap celah injeksi SQL, XSS, CSRF, dan Broken Access Control pada API backend.\nTarget: Zero critical vulnerabilities.",
            'target' => 'Laporan audit keamanan tuntas tanpa temuan kritikal',
            'divisi' => 'IT & Software',
            'deadline' => '2026-01-18',
            'id_hr' => $manajer->id,
            'id_karyawan' => $karyawan1->id,
            'status' => 'Reviewed',
            'hasil_kerja' => 'Security_Penetration_Report_OWASP.pdf',
            'created_at' => Carbon::parse('2026-01-05 08:30:00'),
            'updated_at' => Carbon::parse('2026-01-17 17:00:00'),
        ]);

        TaskChecklist::create(['task_id' => $t6->id, 'judul' => 'Scanning vulnerability via OWASP ZAP tool', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t6->id, 'judul' => 'Patching input validation & parameter binding', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $t6->id, 'judul' => 'Konfigurasi Security Headers (CSP, HSTS, X-Frame-Options)', 'is_completed' => true]);

        Progress::create([
            'task_id' => $t6->id,
            'persentase' => 100,
            'keterangan' => 'Audit keamanan tuntas, security headers telah diaktifkan di production.',
            'tanggal_update' => Carbon::parse('2026-01-17 16:50:00'),
        ]);

        Evaluasi::create([
            'task_id' => $t6->id,
            'id_manajer' => $manajer->id,
            'status_evaluasi' => 'Sesuai Ekspektasi',
            'catatan' => 'Laporan remedi vulnerability tervalidasi dengan baik. Seluruh input telah disanitasi dan CSRF protection berjalan sempurna.',
            'score' => 90,
            'kualitas' => 90,
            'komunikasi' => 88,
            'kerja_sama' => 90,
            'disiplin_tanggung_jawab' => 92,
            'problem_solving' => 90,
            'tanggal_evaluasi' => Carbon::parse('2026-01-18 10:30:00'),
        ]);

        // 4. SEED ABSENSI BULANAN UNTUK PEGAWAI SATU
        // Bulan November 2026 (Hari kerja 1 s.d. 8)
        $hariNovember = [
            ['tgl' => '2026-11-02', 'jam' => '08:24:00', 'status' => 'Tepat waktu', 'detail' => 'Masuk tepat waktu'],
            ['tgl' => '2026-11-03', 'jam' => '08:18:00', 'status' => 'Tepat waktu', 'detail' => 'Masuk tepat waktu'],
            ['tgl' => '2026-11-04', 'jam' => '09:12:00', 'status' => 'Terlambat', 'detail' => 'Terlambat 7 menit'],
            ['tgl' => '2026-11-05', 'jam' => '08:28:00', 'status' => 'Tepat waktu', 'detail' => 'Masuk tepat waktu'],
            ['tgl' => '2026-11-06', 'jam' => '08:15:00', 'status' => 'Tepat waktu', 'detail' => 'Masuk tepat waktu'],
            ['tgl' => '2026-11-07', 'jam' => '08:20:00', 'status' => 'Tepat waktu', 'detail' => 'Masuk tepat waktu'],
            ['tgl' => '2026-11-08', 'jam' => '08:25:00', 'status' => 'Tepat waktu', 'detail' => 'Masuk tepat waktu'],
        ];

        foreach ($hariNovember as $h) {
            Absensi::create([
                'user_id' => $karyawan1->id,
                'tanggal' => $h['tgl'],
                'jenis' => 'masuk',
                'jam' => $h['jam'],
                'status' => $h['status'],
                'detail' => $h['detail'],
            ]);
            Absensi::create([
                'user_id' => $karyawan1->id,
                'tanggal' => $h['tgl'],
                'jenis' => 'pulang',
                'jam' => '17:15:00',
                'status' => 'Pulang sesuai jadwal',
                'detail' => 'Pulang tepat waktu',
            ]);
        }

        // Bulan Januari 2026 (15 hari kerja)
        for ($d = 5; $d <= 25; $d++) {
            $dateStr = sprintf('2026-01-%02d', $d);
            $dayOfWeek = Carbon::parse($dateStr)->dayOfWeek;
            if ($dayOfWeek !== Carbon::SATURDAY && $dayOfWeek !== Carbon::SUNDAY) {
                $isLate = ($d === 12 || $d === 19);
                Absensi::create([
                    'user_id' => $karyawan1->id,
                    'tanggal' => $dateStr,
                    'jenis' => 'masuk',
                    'jam' => $isLate ? '09:14:00' : '08:22:00',
                    'status' => $isLate ? 'Terlambat' : 'Tepat waktu',
                    'detail' => $isLate ? 'Terlambat 9 menit' : 'Masuk tepat waktu',
                ]);
            }
        }

        // Izin / Cuti Karyawan
        PengajuanIzin::create([
            'user_id' => $karyawan1->id,
            'jenis' => 'cuti',
            'tanggal_mulai' => '2026-11-09',
            'tanggal_selesai' => '2026-11-09',
            'alasan' => 'Keperluan keluarga tahunan',
            'status' => 'approved',
            'disetujui_oleh' => $admin->id,
        ]);

        // 5. SEED TASKS FOR PEGAWAI DUA (Marketing & Sales)
        $tMkt1 = Task::create([
            'judul_tugas' => 'Campaign Digital Marketing Q4 & Peluncuran Program Promo',
            'deskripsi' => 'Merancang dan mengeksekusi kampanye iklan berbayar (Meta Ads & Google Search Ads) untuk akuisisi pelanggan B2B.',
            'target' => 'Mendapatkan 150 prospek MQL berkualitas dalam 1 bulan',
            'divisi' => 'Marketing & Sales',
            'deadline' => '2026-11-10',
            'id_hr' => $manajer->id,
            'id_karyawan' => $karyawan2->id,
            'status' => 'Reviewed',
            'hasil_kerja' => 'Laporan_Campaign_Marketing_Q4.pdf',
            'created_at' => Carbon::parse('2026-11-01 08:30:00'),
            'updated_at' => Carbon::parse('2026-11-07 15:00:00'),
        ]);

        TaskChecklist::create(['task_id' => $tMkt1->id, 'judul' => 'Riset kata kunci dan copywriting iklan', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tMkt1->id, 'judul' => 'Setup budget dan target audience Meta Ads', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tMkt1->id, 'judul' => 'Optimasi conversion rate landing page', 'is_completed' => true]);

        Progress::create([
            'task_id' => $tMkt1->id,
            'persentase' => 100,
            'keterangan' => 'Kampanye iklan berjalan efektif dengan CPL 20% di bawah estimasi awal.',
            'tanggal_update' => Carbon::parse('2026-11-07 14:45:00'),
        ]);

        Evaluasi::create([
            'task_id' => $tMkt1->id,
            'id_manajer' => $manajer->id,
            'status_evaluasi' => 'Sesuai Ekspektasi',
            'catatan' => 'Capaian prospek melampaui target bulanan (168 MQL). Pengelolaan anggaran iklan sangat efisien.',
            'score' => 94,
            'kualitas' => 94,
            'komunikasi' => 92,
            'kerja_sama' => 95,
            'disiplin_tanggung_jawab' => 95,
            'problem_solving' => 94,
            'tanggal_evaluasi' => Carbon::parse('2026-11-08 09:00:00'),
        ]);
    }
}
