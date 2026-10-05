# 📑 TANDUAN & PENJELASAN SISTEM PENILAIAN KPI KARYAWAN (PERFORMAN-KU)

Dokumen ini berisi panduan resmi mengenai indikator, pembobotan, rumus perhitungan, dan simulasi penilaian kinerja karyawan (*Key Performance Indicator*) yang diterapkan pada aplikasi **Performan-Ku**.

---

## 🎯 1. TUJUAN PENILAIAN

Sistem Penilaian Kinerja Karyawan bertujuan untuk:
1. Mengukur hasil kerja dan efisiensi karyawan secara **objektif** dan berbasis data.
2. Mendorong ketepatan waktu penyelesaian tugas (*deadline compliance*) dan kualitas hasil kerja.
3. Menilai kedisiplinan serta pengembangan kompetensi (*soft skills*) karyawan.
4. Menjadi dasar pengambilan keputusan manajemen terkait insentif, evaluasi berkala, dan pengembangan karir.

---

## 📊 2. SKEMA PEMBOBOTAN UTAMA (WEIGHTED KPI)

Penilaian Kinerja Keseluruhan (*Total Performance Score*) dihitung menggunakan skema **Weighted KPI** dengan skala nilai **0 – 100**, yang membagi penilaian ke dalam **3 Pilar Utama**:

```
 ┌─────────────────────────────────────────────────────────────────────────┐
 │                   TOTAL SKOR PERFORMANCE KARYAWAN                       │
 ├────────────────────────────┬────────────────────────────┬───────────────┤
 │     PILAR 1: PERFORMANCE   │   PILAR 2: SOFT SKILL      │PILAR 3:ABSENSI│
 │           (60%)            │           (30%)            │     (10%)     │
 └────────────────────────────┴────────────────────────────┴───────────────┘
```

---

## 🔍 3. RINCIAN 3 PILAR UTAMA PENILAIAN

### 🟢 PILAR 1: Performance KPI (Bobot Total 60%)
Pilar ini mengukur kuantitas, kecepatan, dan kualitas dari tugas-tugas (*tasks*) yang dikerjakan karyawan. Skor pilar ini (*Raw Performance*) dibentuk oleh 3 sub-indikator:

| Sub-Indikator | Sub-Bobot | Deskripsi & Rumus |
| :--- | :---: | :--- |
| **A. Pencapaian Target** | **40%** | Mengukur seberapa banyak tugas yang berhasil diselesaikan dibandingkan total tugas yang ditugaskan.<br>$$\text{Nilai Target} = \left(\frac{\text{Tugas Selesai}}{\text{Total Tugas}}\right) \times 100$$ |
| **B. Ketepatan Waktu** | **30%** | Mengukur persentase tugas selesai tepat waktu ($\le$ tanggal *deadline*).<br>$$\text{Nilai Ketepatan Waktu} = \left(\frac{\text{Tugas Selesai Tepat Waktu}}{\text{Total Tugas Selesai}}\right) \times 100$$ |
| **C. Kualitas Hasil Kerja** | **30%** | Penilaian manajer atas mutu hasil pekerjaan (Skala 0-100:<br>• 100 = Sangat Baik, • 80 = Baik, • 60 = Cukup, • 40 = Kurang). |

**Rumus Subtotal Pilar 1:**
$$\text{Raw Performance Score} = (\text{Nilai Target} \times 40\%) + (\text{Nilai Ketepatan Waktu} \times 30\%) + (\text{Nilai Kualitas} \times 30\%)$$
$$\mathbf{\text{Kontribusi Pilar 1 (Performance)}} = \text{Raw Performance Score} \times \mathbf{60\%}$$

---

### 🔵 PILAR 2: Competency / Soft Skill (Bobot Total 30%)
Pilar ini menilai sikap, perilaku kerja, dan kompetensi individu karyawan. Penilaian dilakukan oleh Manajer saat melakukan evaluasi tugas, yang mencakup 4 aspek penting:

| Aspek Soft Skill | Bobot | Deskripsi Indikator |
| :--- | :---: | :--- |
| **1. Komunikasi** | **25%** | Kejelasan laporan progres, keaktifan berkoordinasi, dan responsivitas. |
| **2. Kerja Sama Tim** | **25%** | Kemampuan berkolaborasi dengan rekan kerja dan berkontribusi dalam tim. |
| **3. Disiplin & Tanggung Jawab** | **25%** | Kepatuhan pada prosedur/SOP serta komitmen menyelesaikan tugas. |
| **4. Problem Solving** | **25%** | Kemampuan menganalisis kendala dan menemukan solusi atas masalah kerja. |

**Rumus Subtotal Pilar 2:**
$$\text{Raw Soft Skill Score} = (\text{Komunikasi} \times 25\%) + (\text{Kerja Sama} \times 25\%) + (\text{Disiplin} \times 25\%) + (\text{Problem Solving} \times 25\%)$$
$$\mathbf{\text{Kontribusi Pilar 2 (Soft Skill)}} = \text{Raw Soft Skill Score} \times \mathbf{30\%}$$

---

### 🟡 PILAR 3: Absensi / Presensi (Bobot Total 10%)
Pilar ini mengukur kedisiplinan kehadiran fisik karyawan di tempat kerja berdasarkan pencatatan absen masuk harian.

* **Rumus:**
  $$\text{Raw Absensi Score} = \left(\frac{\text{Jumlah Kehadiran Tepat Waktu}}{\text{Total Presensi Masuk}}\right) \times 100$$
$$\mathbf{\text{Kontribusi Pilar 3 (Absensi)}} = \text{Raw Absensi Score} \times \mathbf{10\%}$$

---

## 🧮 4. RUMUS TOTAL NILAI KPI AKHIR

Total skor kinerja akhir (*Final Score*) dihitung dengan menjumlahkan kontribusi dari ketiga pilar:

$$\mathbf{\text{Total Nilai Akhir}} = (\text{Raw Performance} \times 60\%) + (\text{Raw Soft Skill} \times 30\%) + (\text{Raw Absensi} \times 10\%)$$

---

## 💡 5. SIMULASI PERHITUNGAN STUDI KASUS

Berikut adalah contoh perhitungan nyata untuk karyawan bernama **Budi**:

### Step 1: Perhitungan Performance KPI (60%)
- Total Tugas Ditugaskan: **10 Tugas**
- Tugas Berhasil Selesai: **8 Tugas** $\rightarrow \text{Nilai Target} = (8 / 10) \times 100 = \mathbf{80}$
- Tugas Selesai Tepat Waktu: **6 Tugas** $\rightarrow \text{Nilai Ketepatan Waktu} = (6 / 8) \times 100 = \mathbf{75}$
- Penilaian Kualitas dari Manajer: **90**
- **Raw Performance Score** $= (80 \times 40\%) + (75 \times 30\%) + (90 \times 30\%) = 32 + 22.5 + 27 = \mathbf{81.50}$
- **Kontribusi Pilar 1 (60%)** $= 81.50 \times 60\% = \mathbf{48.90}$

### Step 2: Perhitungan Competency / Soft Skill (30%)
- Nilai Komunikasi: **80**
- Nilai Kerja Sama Tim: **85**
- Nilai Disiplin & Tanggung Jawab: **90**
- Nilai Problem Solving: **75**
- **Raw Soft Skill Score** $= (80 \times 25\%) + (85 \times 25\%)  + (90 \times 25\%) + (75 \times 25\%) = \mathbf{82.50}$
- **Kontribusi Pilar 2 (30%)** $= 82.50 \times 30\% = \mathbf{24.75}$

### Step 3: Perhitungan Absensi (10%)
- Total Hari Kerja Presensi: **20 Hari**
- Kehadiran Tepat Waktu: **18 Hari** (2 Hari Terlambat)
- **Raw Absensi Score** $= (18 / 20) \times 100 = \mathbf{90.00}$
- **Kontribusi Pilar 3 (10%)** $= 90.00 \times 10\% = \mathbf{9.00}$

---

### 🏆 HASIL SKOR AKHIR & PREDIKAT BUDI:

$$\text{Nilai Akhir Budi} = 48.90 + 24.75 + 9.00 = \mathbf{82.65}$$

### 🏆 TABEL PREDIKAT KINERJA

| Rentang Nilai Total | Predikat Kinerja | Keterangan Evaluasi | Hasil Budi |
| :---: | :---: | :--- | :---: |
| **85.00 – 100.00** | **Sangat Baik** | Kinerja sangat memuaskan, melampaui ekspektasi. | |
| **70.00 – 84.99** | **Baik** | Kinerja baik, memenuhi ekspektasi standar perusahaan. | **82.65 (Baik)** |
| **55.00 – 69.99** | **Cukup** | Kinerja cukup, namun memerlukan beberapa perbaikan. | |
| **< 55.00** | **Perlu Perbaikan** | Kinerja di bawah standar, perlu pembinaan khusus. | |

---

## ⚙️ 6. INTEGRASI ALUR PADA APLIKASI PERFORMAN-KU

1. **HR / Manajer** membuat *task* baru dan menetapkan target serta *deadline*.
2. **Karyawan** memperbarui progres pekerjaan dan mengunggah dokumen/lampiran bukti tugas.
3. **Karyawan** melakukan presensi harian pada menu Absensi.
4. **Manajer** mereview tugas selesai, menginput penilaian Kualitas & 4 aspek Soft Skill.
5. **Sistem (Backend Model [User.php](file:///d:/Performan-Ku/app/Models/User.php))** secara otomatis mengkalkulasi ketiga pilar dan menampilkan grafik serta predikat kinerja karyawan secara *real-time*.

---
*Dokumen ini dibuat otomatis oleh sistem Performan-Ku sebagai acuan baku penilaian kinerja.*
