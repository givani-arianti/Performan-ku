@extends('layouts.app')

@section('title', 'Laporan Kinerja Karyawan - ' . $employee->name)
@section('page_title', 'Laporan Kinerja Karyawan')

@section('styles')
<style>
@media print {
    aside, header, .no-print, nav, button, a.btn-no-print { display: none !important; }
    body, html { background: #ffffff !important; overflow: visible !important; height: auto !important; font-size: 11pt !important; }
    .space-y-6 { space-y: 0 !important; }
    .shadow-sm, .shadow, .shadow-lg { box-shadow: none !important; }
    .border { border-color: #cbd5e1 !important; }
    .print-header { display: block !important; margin-bottom: 24px; }
    .print-break-inside-avoid { break-inside: avoid; }
    .print-p-0 { padding: 0 !important; }
    @page {
        size: A4 portrait;
        margin: 1.5cm;
    }
}
</style>
@endsection

@section('content')
<div class="space-y-6">
    @php
        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $labelPeriode = ($bulan && $bulan !== 'all' ? ($namaBulan[$bulan] ?? 'Bulan ' . $bulan) : 'Semua Periode') . ' ' . $tahun;
        $docNo = 'PK/HR-EVAL/' . $tahun . '/' . ($bulan !== 'all' ? $bulan : 'ALL') . '/' . str_pad($employee->id, 3, '0', STR_PAD_LEFT);
        
        $targetScore = $kpi['target_score'] ?? 0;
        $timelinessScore = $kpi['timeliness_score'] ?? 0;
        $speedScore = $kpi['speed_score'] ?? 0;
        $finalScore = $kpi['final_score'] ?? 0;

        $grade = match(true) {
            $finalScore >= 85 => ['huruf' => 'A', 'label' => 'Sangat Baik (Exceeds Expectations)', 'color' => 'emerald'],
            $finalScore >= 70 => ['huruf' => 'B', 'label' => 'Baik (Meets Expectations)', 'color' => 'blue'],
            $finalScore >= 55 => ['huruf' => 'C', 'label' => 'Cukup (Needs Improvement)', 'color' => 'amber'],
            default => ['huruf' => 'D', 'label' => 'Perlu Perbaikan (Unsatisfactory)', 'color' => 'red'],
        };
    @endphp

    <!-- Top Action & Filter Bar (No Print) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('manajer.performance') }}" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="font-extrabold text-slate-800 text-base">Laporan Penilaian Kinerja: {{ $employee->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Filter berdasarkan periode bulan dan cetak dokumen resmi penilaian KPI karyawan.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Filter Form Bulan & Tahun -->
            <form action="{{ route('manajer.employee.detail', $employee->id) }}" method="GET" class="flex items-center gap-2">
                <select name="bulan" class="rounded-xl border border-slate-300 px-3 py-2 bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm focus:border-[#4d7a6b] focus:outline-none">
                    <option value="all" {{ $bulan === 'all' ? 'selected' : '' }}>Semua Bulan</option>
                    @foreach($namaBulan as $num => $nama)
                        <option value="{{ $num }}" {{ (string)$bulan === (string)$num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>

                <select name="tahun" class="rounded-xl border border-slate-300 px-3 py-2 bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm focus:border-[#4d7a6b] focus:outline-none">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="bg-[#21352f] text-white font-bold text-xs px-4 py-2 rounded-xl hover:bg-[#1a2a24] shadow transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan
                </button>
            </form>

            <button onclick="window.print()" class="bg-[#2d433b] hover:bg-[#1a2a24] text-white font-bold text-xs px-4 py-2 rounded-xl shadow transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Dokumen Resmi (PDF)
            </button>
        </div>
    </div>

    <!-- Official Corporate Appraisal Document Paper -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-6">
        
        <!-- 1. Formal Corporate Letterhead (KOP SURAT RESMI) -->
        <div class="border-b-4 border-double border-slate-800 pb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#21352f] text-white flex items-center justify-center font-black text-2xl shadow-sm">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight uppercase leading-none">
                            PT PERFORMAN-KU SOLUSI DIGITAL INDONESIA
                        </h1>
                        <p class="text-xs text-slate-600 font-bold mt-1 uppercase tracking-wider">
                            Divisi Manajemen Kinerja & Sumber Daya Manusia (Human Capital Management)
                        </p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Menara Kinerja Prima Lt. 18, Jl. Jend. Sudirman Kav. 45-46, Jakarta Selatan 12190 &bull; Telp: (021) 5795-8888 &bull; Email: hrd@performan-ku.co.id
                        </p>
                    </div>
                </div>
                <div class="hidden sm:block text-right">
                    <div class="inline-block px-3 py-1 bg-red-50 border border-red-200 text-red-700 text-[10px] font-extrabold uppercase tracking-widest rounded-md">
                        SANGAT RAHASIA
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-mono">No: {{ $docNo }}</p>
                </div>
            </div>
        </div>

        <!-- 2. Document Title & Period -->
        <div class="text-center py-2 space-y-1">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-wide">
                LEMBAR HASIL PENILAIAN KINERJA & REKAPITULASI TUGAS KARYAWAN
            </h2>
            <p class="text-xs text-slate-500 font-semibold tracking-wider uppercase">
                Employee Key Performance Indicators (KPI) Appraisal Report
            </p>
            <div class="inline-flex items-center gap-2 mt-1 px-3 py-1 bg-slate-100 rounded-full text-xs font-bold text-slate-800">
                <i class="fa-regular fa-calendar-check text-[#4d7a6b]"></i>
                Periode Penilaian: {{ $labelPeriode }}
            </div>
        </div>

        <!-- 3. Employee Profile & Metadata (Dua Kolom Rapi) -->
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 text-xs">
            <h4 class="font-extrabold text-slate-800 uppercase text-[11px] tracking-wider mb-3 border-b border-slate-200 pb-1.5 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-navy-800"></i> I. Identitas Pegawai Yang Dinilai
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-slate-500 font-medium">Nomor Induk Pegawai (NIP)</span>
                        <span class="text-slate-800 font-mono font-bold">: PK-{{ $tahun }}-00{{ $employee->id }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-slate-500 font-medium">Nama Lengkap</span>
                        <span class="text-slate-800 font-bold">: {{ $employee->name }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-slate-500 font-medium">Alamat Email Perusahaan</span>
                        <span class="text-slate-800 font-semibold">: {{ $employee->email }}</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="w-36 text-slate-500 font-medium">Departemen / Divisi</span>
                        <span class="text-slate-800 font-bold">: {{ $employee->divisi ?: 'IT & Software Engineering' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-slate-500 font-medium">Status Kepegawaian</span>
                        <span class="text-slate-800 font-semibold">: Karyawan Tetap (PKWTT)</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-slate-500 font-medium">Atasan Langsung / Penilai</span>
                        <span class="text-slate-800 font-bold">: {{ auth()->user()->name }} (Manajer Divisi)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. KPI Scoring Table & Breakdown Metrics (Bobot Resmi Perusahaan) -->
        <div class="space-y-3">
            <h4 class="font-extrabold text-slate-800 uppercase text-[11px] tracking-wider border-b border-slate-200 pb-1.5 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-navy-800"></i> II. Rincian Penilaian Indikator Kinerja Utama (KPI)
                </span>
                <span class="text-[10px] text-slate-500 font-normal">Metode: Weighted Score Matrix (Akumulasi Bulanan)</span>
            </h4>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">
                <!-- Breakdown Table (2 cols) -->
                <div class="lg:col-span-2 border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2.5">Aspek / Indikator Penilaian</th>
                                <th class="px-3 py-2.5 text-center">Bobot</th>
                                <th class="px-3 py-2.5 text-center">Realisasi</th>
                                <th class="px-3 py-2.5 text-center">Skor Tertimbang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800">1. Kuantitas & Capaian Target Tugas</div>
                                    <div class="text-[10px] text-slate-500">Persentase penyelesaian deliverables tugas yang didelegasikan</div>
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-slate-600">40%</td>
                                <td class="px-3 py-3 text-center font-bold text-slate-800">{{ $targetScore }}%</td>
                                <td class="px-3 py-3 text-center font-extrabold text-navy-800">{{ round($targetScore * 0.40, 2) }}%</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800">2. Ketepatan Waktu Terhadap Deadline (SLA)</div>
                                    <div class="text-[10px] text-slate-500">Kepatuhan penyelesaian tugas sebelum/saat batas waktu tenggat</div>
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-slate-600">30%</td>
                                <td class="px-3 py-3 text-center font-bold text-slate-800">{{ $timelinessScore }}%</td>
                                <td class="px-3 py-3 text-center font-extrabold text-navy-800">{{ round($timelinessScore * 0.30, 2) }}%</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800">3. Kecepatan & Efisiensi Kerja (Turnaround Time)</div>
                                    <div class="text-[10px] text-slate-500">Tingkat akselerasi dan ketanggapan dalam menuntaskan instruksi</div>
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-slate-600">30%</td>
                                <td class="px-3 py-3 text-center font-bold text-slate-800">{{ $speedScore }}%</td>
                                <td class="px-3 py-3 text-center font-extrabold text-navy-800">{{ round($speedScore * 0.30, 2) }}%</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                            <tr>
                                <td class="px-4 py-2.5 text-slate-800 uppercase text-[11px]">Total Skor Akumulasi Tertimbang</td>
                                <td class="px-3 py-2.5 text-center text-slate-700">100%</td>
                                <td class="px-3 py-2.5 text-center text-slate-400">-</td>
                                <td class="px-3 py-2.5 text-center text-lg font-black text-navy-800">{{ $finalScore }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Final Grade Card (1 col) -->
                <div class="bg-slate-50 border-2 border-slate-300 rounded-xl p-5 flex flex-col justify-between text-center">
                    <div>
                        <span class="block text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-1">
                            PREDIKAT KINERJA AKHIR
                        </span>
                        <div class="text-5xl font-black text-slate-900 my-2">
                            {{ $grade['huruf'] }}
                        </div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wide bg-white border shadow-xs text-slate-800">
                            {{ $grade['label'] }}
                        </span>
                    </div>

                    <div class="border-t border-slate-200 pt-3 mt-3 text-[10px] text-slate-500 text-left space-y-1">
                        <div class="flex justify-between font-semibold"><span>A (Sangat Baik):</span><span>85% - 100%</span></div>
                        <div class="flex justify-between font-semibold"><span>B (Baik):</span><span>70% - 84%</span></div>
                        <div class="flex justify-between font-semibold"><span>C (Cukup):</span><span>55% - 69%</span></div>
                        <div class="flex justify-between font-semibold"><span>D (Kurang):</span><span>&lt; 55%</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Attendance & Discipline Record (Kedisiplinan & Kehadiran) -->
        <div class="space-y-3">
            <h4 class="font-extrabold text-slate-800 uppercase text-[11px] tracking-wider border-b border-slate-200 pb-1.5 flex items-center gap-2">
                <i class="fa-solid fa-user-clock text-navy-800"></i> III. Rekapitulasi Kehadiran & Kedisiplinan Kerja
            </h4>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 text-center text-xs">
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <span class="block text-[10px] text-slate-500 font-bold uppercase mb-0.5">Total Hari Masuk</span>
                    <span class="text-xl font-black text-slate-900">{{ $totalHadir }} Hari</span>
                    <p class="text-[10px] text-slate-400 mt-0.5">Kehadiran tercatat</p>
                </div>
                <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-xl">
                    <span class="block text-[10px] text-emerald-800 font-bold uppercase mb-0.5">Hadir Tepat Waktu</span>
                    <span class="text-xl font-black text-emerald-700">{{ $tepatWaktu }} Hari</span>
                    <p class="text-[10px] text-emerald-600 mt-0.5">Sebelum pk 09:05</p>
                </div>
                <div class="p-3 bg-red-50/60 border border-red-200 rounded-xl">
                    <span class="block text-[10px] text-red-800 font-bold uppercase mb-0.5">Keterlambatan</span>
                    <span class="text-xl font-black text-red-700">{{ $terlambat }} Hari</span>
                    <p class="text-[10px] text-red-600 mt-0.5">Lewat batas toleransi</p>
                </div>
                <div class="p-3 bg-blue-50/60 border border-blue-200 rounded-xl">
                    <span class="block text-[10px] text-blue-800 font-bold uppercase mb-0.5">Izin / Cuti Disetujui</span>
                    <span class="text-xl font-black text-blue-800">{{ $izinCount }} Hari</span>
                    <p class="text-[10px] text-blue-600 mt-0.5">Izin sah dari HR</p>
                </div>
            </div>
        </div>

        <!-- 6. Detailed Task Portfolio Breakdown (Tabel Lengkap Tugas & Revisi) -->
        <div class="space-y-3">
            <h4 class="font-extrabold text-slate-800 uppercase text-[11px] tracking-wider border-b border-slate-200 pb-1.5 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-navy-800"></i> IV. Rincian Portofolio Pengerjaan Tugas & Riwayat Evaluasi
                </span>
                <span class="text-[10px] text-slate-500 font-normal">Total: {{ $filteredTasks->count() }} Tugas pada periode ini</span>
            </h4>

            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-3 text-center w-10">No</th>
                            <th class="px-4 py-3">Uraian Tugas & Spesifikasi</th>
                            <th class="px-3 py-3 whitespace-nowrap">Batas Waktu (SLA)</th>
                            <th class="px-3 py-3 text-center">Progres</th>
                            <th class="px-3 py-3 text-center">Kecepatan DL</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-4 py-3">Hasil Evaluasi / Catatan Revisi Manajer</th>
                            <th class="px-3 py-3 text-center no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($filteredTasks as $idx => $t)
                            @php $kecepatan = $t->kecepatan_info; @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900 text-xs">{{ $t->judul_tugas }}</div>
                                    <div class="text-[11px] text-slate-600 mt-1 leading-relaxed">{{ $t->deskripsi }}</div>
                                    @if($t->target)
                                        <div class="mt-1.5 text-[10px] text-slate-500 font-medium">
                                            <strong>Target:</strong> {{ $t->target }}
                                        </div>
                                    @endif
                                    @if($t->dokumen_path)
                                        <div class="mt-1 text-[10px] text-blue-700 font-semibold flex items-center gap-1.5">
                                            <i class="fa-solid fa-file-pdf text-red-500"></i> Bukti Kerja: {{ $t->hasil_kerja }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-slate-700 font-semibold text-xs">
                                    {{ \Carbon\Carbon::parse($t->deadline)->format('d F Y') }}
                                </td>
                                <td class="px-3 py-3 text-center whitespace-nowrap font-black text-slate-800">
                                    {{ $t->persentase }}%
                                </td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $kecepatan['color'] }} border">
                                        {{ $kecepatan['label'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @if($t->status === 'Pending')
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700">Pending</span>
                                    @elseif($t->status === 'In Progress')
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-800">Dalam Proses</span>
                                    @elseif($t->status === 'Revisi')
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                                            <i class="fa-solid fa-arrows-rotate mr-0.5"></i> Perlu Revisi
                                        </span>
                                    @elseif($t->status === 'Completed')
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-800">Menunggu Evaluasi</span>
                                    @elseif($t->status === 'Reviewed')
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-check mr-0.5"></i> Dievaluasi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($t->evaluasi)
                                        <div class="flex items-center gap-1.5 font-extrabold text-xs {{ $t->evaluasi->status_evaluasi === 'Perlu Revisi' ? 'text-amber-800' : 'text-slate-800' }}">
                                            <span>{{ $t->evaluasi->status_evaluasi }}</span>
                                            <span class="text-slate-500 font-semibold">(Skor Mutu: {{ $t->evaluasi->score }}/100)</span>
                                        </div>
                                        @if($t->evaluasi->catatan)
                                            <div class="text-[10px] text-slate-700 mt-1 italic {{ $t->evaluasi->status_evaluasi === 'Perlu Revisi' ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-slate-50 border-slate-200' }} p-2 rounded-lg border leading-relaxed">
                                                "{{ $t->evaluasi->catatan }}"
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic text-[10px]">Belum dilakukan evaluasi</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center whitespace-nowrap no-print">
                                    @if(in_array($t->status, ['Completed', 'Reviewed', 'Revisi']))
                                        <a href="{{ route('manajer.task.evaluate', $t->id) }}" class="inline-flex items-center justify-center px-2.5 py-1 text-[11px] font-bold text-white bg-navy-800 hover:bg-navy-700 rounded-lg shadow-sm transition">
                                            <i class="fa-solid fa-pen-to-square mr-1"></i> Review
                                        </a>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Belum selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-400 italic">
                                    <i class="fa-solid fa-folder-open text-2xl mb-1 text-slate-300"></i>
                                    <p>Tidak ada catatan tugas terdaftar pada periode {{ $labelPeriode }}.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 7. Evaluator Feedback & Development Plan (Ulasan & Rekomendasi Manajer) -->
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-3 text-xs">
            <h4 class="font-extrabold text-slate-800 uppercase text-[11px] tracking-wider border-b border-slate-200 pb-1.5 flex items-center gap-2">
                <i class="fa-solid fa-comment-dots text-navy-800"></i> V. Catatan Kualitatif & Rekomendasi Pengembangan Manajer
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="space-y-1">
                    <span class="font-bold text-slate-700 block text-[11px]">1. Kekuatan Utama (Key Strengths)</span>
                    <p class="text-slate-600 leading-relaxed bg-white p-3 rounded-lg border border-slate-200">
                        @if($finalScore >= 80)
                            Pegawai menunjukkan pemahaman teknis yang mendalam, dedikasi tinggi terhadap tenggat waktu, dan kemampuan memecahkan masalah kompleks secara mandiri.
                        @else
                            Pegawai proaktif dalam berkoordinasi dengan tim dan selalu berupaya menyelesaikan tugas sesuai instruksi yang diberikan.
                        @endif
                    </p>
                </div>
                <div class="space-y-1">
                    <span class="font-bold text-slate-700 block text-[11px]">2. Area Perbaikan (Areas for Improvement)</span>
                    <p class="text-slate-600 leading-relaxed bg-white p-3 rounded-lg border border-slate-200">
                        @if($filteredTasks->where('status', 'Revisi')->count() > 0)
                            Perlu peningkatan ketelitian pada tahap self-review dan pengecekan edge-cases sebelum mengirimkan hasil akhir agar meminimalkan siklus revisi.
                        @else
                            Pertahankan konsistensi kualitas kerja dan tingkatkan kontribusi dalam sesi knowledge sharing internal divisi.
                        @endif
                    </p>
                </div>
                <div class="space-y-1">
                    <span class="font-bold text-slate-700 block text-[11px]">3. Rekomendasi Tindak Lanjut (HR Action)</span>
                    <p class="text-slate-600 leading-relaxed bg-white p-3 rounded-lg border border-slate-200">
                        @if($finalScore >= 85)
                            Direkomendasikan untuk mendapatkan insentif performa bulanan dan diikutsertakan dalam pelatihan sertifikasi advance backend engineering.
                        @elseif($finalScore >= 70)
                            Pertahankan penugasan reguler dan jadwalkan evaluasi berkala untuk persiapan kenaikan target kuartal berikutnya.
                        @else
                            Diberikan sesi mentoring intensif bersama senior staff untuk mendalami standar operasional prosedur teknis.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- 8. Formal Three-Party Signatures (Pengesahan Tiga Pihak) -->
        <div class="pt-6 border-t-2 border-slate-800 grid grid-cols-3 gap-6 text-center text-xs print-break-inside-avoid">
            <div>
                <p class="text-slate-500 font-medium mb-16">Karyawan Yang Dinilai,</p>
                <p class="font-black text-slate-900 underline uppercase tracking-wide">{{ $employee->name }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5">NIP: PK-{{ $tahun }}-00{{ $employee->id }}</p>
                <p class="text-[10px] text-slate-400">Tanggal: ..... / ..... / {{ $tahun }}</p>
            </div>
            <div>
                <p class="text-slate-500 font-medium mb-16">Atasan Langsung / Manajer Penilai,</p>
                <p class="font-black text-slate-900 underline uppercase tracking-wide">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Head of {{ $employee->divisi ?: 'IT & Software' }}</p>
                <p class="text-[10px] text-slate-400">Tanggal: ..... / ..... / {{ $tahun }}</p>
            </div>
            <div>
                <p class="text-slate-500 font-medium mb-16">Mengetahui & Menyetujui HRD,</p>
                <p class="font-black text-slate-900 underline uppercase tracking-wide">ADMIN HR / HC DIRECTOR</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Human Capital Management Dept.</p>
                <p class="text-[10px] text-slate-400">Cap Perusahaan & Tanda Tangan</p>
            </div>
        </div>
    </div>
</div>
@endsection
