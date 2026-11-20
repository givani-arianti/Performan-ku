# 📑 TANDUAN & PENJELASAN SISTEM PENILAIAN EVALUASI TUGAS BULANAN (PERFORMAN-KU)

Dokumen ini berisi panduan resmi mengenai indikator, pembobotan, rumus perhitungan, dan evaluasi kinerja karyawan yang diterapkan pada aplikasi **Performan-Ku**.

---

## 🎯 1. TUJUAN PENILAIAN

Sistem Penilaian Kinerja Karyawan bertujuan untuk:
1. Mengukur hasil kerja dan efisiensi karyawan secara **objektif** berbasis data tugas bulanan.
2. Mendorong ketepatan waktu penyelesaian tugas (*deadline compliance*) dan kecepatan pengerjaan.
3. Menilai hasil akumulasi dari **keseluruhan tugas yang dikerjakan karyawan dalam satu bulan** (bukan dihitung terpisah per masing-masing tugas).

---

## 📊 2. INDIKATOR PENILAIAN EKSKLUSIF TUGAS BULANAN

Penilaian kinerja karyawan dihitung dari akumulasi seluruh tugas dalam **1 Bulan** berdasarkan 3 Sub-Indikator Utama:

```
 ┌─────────────────────────────────────────────────────────────────────────┐
 │               TOTAL SKOR EVALUASI BULANAN KARYAWAN (100%)               │
 ├────────────────────────────┬────────────────────────────┬───────────────┤
 │   1. BANYAK TUGAS SELESAI  │    2. KETEPATAN WAKTU DL   │ 3. KECEPATAN  │
 │           (40%)            │           (30%)            │     (30%)     │
 └────────────────────────────┴────────────────────────────┴───────────────┘
```

| Indikator Penilaian | Bobot | Deskripsi & Rumus |
| :--- | :---: | :--- |
| **A. Banyak Tugas Diselesaikan (Kuantitas)** | **40%** | Mengukur rata-rata persentase pengerjaan seluruh tugas dalam 1 bulan.<br>$$\text{Nilai Kuantitas} = \frac{\sum \text{Persentase Pengerjaan Tugas}}{N}$$ |
| **B. Ketepatan Waktu DL** | **30%** | Mengukur tingkat penyelesaian tugas sebelum atau tepat pada tanggal *deadline*.<br>$$\text{Nilai Ketepatan} = \frac{\sum \text{Skor Ketepatan Tugas}}{N}$$ |
| **C. Kecepatan Pengerjaan Sesuai DL** | **30%** | Mengukur seberapa cepat tugas diselesaikan relatif terhadap *deadline*.<br>$$\text{Nilai Kecepatan} = \frac{\sum \text{Skor Kecepatan Tugas}}{N}$$ |

---

## 🧮 3. RUMUS TOTAL NILAI EVALUASI BULANAN

Total skor kinerja akhir (*Final Score*) dihitung dengan menjumlahkan kontribusi dari ketiga indikator tugas bulanan:

$$\mathbf{\text{Total Nilai Bulanan}} = (\text{Nilai Kuantitas} \times 40\%) + (\text{Nilai Ketepatan} \times 30\%) + (\text{Nilai Kecepatan} \times 30\%)$$

### 🏆 TABEL PREDIKAT KINERJA

| Rentang Nilai Total | Predikat Kinerja | Keterangan Evaluasi |
| :---: | :---: | :--- |
| **85.00 – 100.00** | **Sangat Baik** | Kinerja sangat memuaskan, tugas diselesaikan cepat & tepat waktu. |
| **70.00 – 84.99** | **Baik** | Kinerja baik, memenuhi standar ekspektasi perusahaan. |
| **55.00 – 69.99** | **Cukup** | Kinerja cukup, memerlukan peningkatan ketepatan/kecepatan. |
| **< 55.00** | **Perlu Perbaikan** | Kinerja di bawah standar, perlu pembinaan khusus. |

---

## ⚙️ 4. TAMPILAN PADA MENU TUGAS APLIKASI PERFORMAN-KU

Ketika membuka daftar tugas (`Tugas Saya` / `Detail Tugas`), sistem secara otomatis menampilkan:
1. **% Pengerjaan Tugas** (Berapa persen tugas diselesaikan).
2. **Kecepatan Sesuai DL** (Berapa cepat diselesaikan relatif terhadap *deadline*, misal: *"Sangat Cepat (2 hari sebelum DL)"*, *"Tepat Waktu"*, *"Terlambat"*).
3. **Hasil Evaluasi Akumulasi Bulanan** pada halaman detail dan dashboard manajer.
