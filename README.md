# Performan-Ku

Aplikasi manajemen kinerja karyawan berbasis Laravel 12. Mendukung tiga peran utama:

- **Admin/HR** — mengelola akun, memantau tugas & absensi, serta menyetujui cuti/izin.
- **Manajer** — membuat tugas, memantau progres, dan mengevaluasi hasil kerja karyawan.
- **Karyawan** — mengerjakan tugas & checklist, mengirim hasil kerja, absensi, dan mengajukan cuti/izin.

## Kebutuhan

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL/MariaDB (atau SQLite untuk pengembangan)

## Instalasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env`, lalu:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Untuk mode pengembangan frontend:

```bash
npm run dev
```

## Akun Demo

Data contoh dari `DatabaseSeeder`:

| Peran | Email | Password |
| --- | --- | --- |
| Admin/HR | `admin@gmail.com` | `password123` |
| Manajer | `manajer@gmail.com` | `password123` |
| Karyawan | `karyawan@gmail.com` | `password123` |
| Karyawan | `karyawan2@gmail.com` | `password123` |

> Akun demo hanya untuk pengembangan. Ganti password sebelum dipakai di lingkungan nyata.
> Akun baru yang dibuat lewat menu Admin mendapat password acak yang ditampilkan satu kali.

## Pengujian

```bash
php artisan test
```

## Catatan Keamanan

- Absensi memakai waktu server; waktu dari perangkat klien tidak dipercaya.
- Dokumen cuti/izin disimpan di disk privat dan hanya dapat diunduh oleh pemilik atau HR.
- Manajer hanya dapat mengelola tugas dan melihat kinerja karyawan dari divisinya sendiri.
- Login dibatasi 5 percobaan per menit per IP.

## Struktur Singkat

```
app/
  Http/Controllers/   # Admin, Manajer, Karyawan, Auth, Profile
  Models/             # User, Task, Evaluasi, Absensi, dll.
  Policies/           # Otorisasi berbasis peran & divisi
  Services/           # AttendanceService (rekap absensi & izin)
resources/views/      # Blade views per peran
database/migrations/  # Skema database
routes/web.php        # Routing utama
```
