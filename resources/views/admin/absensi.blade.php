@extends('layouts.app')

@section('title', 'Rekap Absensi Karyawan')
@section('page_title', 'Rekap Absensi Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Header & Summary Banner -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-user text-[#4d7a6b]"></i> Rekap Absensi Karyawan
            </h2>
            <p class="text-sm text-slate-500 mt-1">Pantau presensi, tingkat kehadiran, dan keterlambatan seluruh karyawan secara real-time.</p>
        </div>
        <button onclick="window.print()" class="no-print bg-[#2d433b] hover:bg-[#1a2a24] text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition flex items-center gap-2 self-start md:self-auto">
            <i class="fa-solid fa-print"></i> Cetak Laporan
        </button>
    </div>

<style>
@media print {
    aside, header, .no-print, nav, button { display: none !important; }
    body, html { background: #ffffff !important; overflow: visible !important; height: auto !important; }
    .space-y-6 { space-y: 0 !important; }
    .shadow-sm, .shadow { box-shadow: none !important; }
    .border { border-color: #cbd5e1 !important; }
    .print-header { display: block !important; margin-bottom: 20px; }
}
</style>

<div class="hidden print-header text-center border-b pb-4 mb-4">
    <h1 class="text-2xl font-bold text-slate-900">LAPORAN REKAP PRESENSI KARYAWAN</h1>
    <p class="text-sm text-slate-600">Aplikasi Performan-Ku - Periode: {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} {{ $tahun }}</p>
</div>

    <!-- Filter Form -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <form action="{{ route('admin.absensi') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
            <!-- Filter Bulan -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Bulan</label>
                <select name="bulan" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2.5">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tahun -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tahun</label>
                <select name="tahun" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2.5">
                    @foreach(range(now()->year - 2, now()->year) as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Karyawan -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Karyawan</label>
                <select name="user_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2.5">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $userId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tanggal Spesifik -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Spesifik</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2">
            </div>

            <!-- Filter Button -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-[#21352f] text-white font-semibold text-sm hover:bg-[#1a2a24] transition shadow-sm">
                    <i class="fa-solid fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.absensi') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold text-sm hover:bg-slate-200 transition" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-right-to-bracket"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Total Absen Masuk</p>
                <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalMasuk }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-clock font-bold"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Tepat Waktu</p>
                <p class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $totalTepatWaktu }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Terlambat</p>
                <p class="text-2xl font-bold text-red-600 mt-0.5">{{ $totalTerlambat }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Absen Pulang</p>
                <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalPulang }}</p>
            </div>
        </div>
    </div>

    <!-- Approval Pengajuan Izin / Cuti Section -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden no-print">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-[#4d7a6b]"></i> Pengajuan Cuti / Izin Menunggu Persetujuan
            </h3>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800">
                {{ $pendingIzinList->where('status', 'pending')->count() }} Menunggu
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#fbfaf8] text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-6 py-3.5">Jenis</th>
                        <th class="px-6 py-3.5">Rentang Tanggal</th>
                        <th class="px-6 py-3.5">Alasan</th>
                        <th class="px-6 py-3.5">Dokumen</th>
                        <th class="px-6 py-3.5 text-center">Aksi / Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($pendingIzinList as $izin)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $izin->user->name ?? 'Karyawan' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $izin->user->divisi ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 uppercase font-bold text-slate-700">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $izin->jenis === 'sakit' ? 'bg-red-50 text-red-700' : ($izin->jenis === 'cuti' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700') }}">
                                    {{ $izin->jenis }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 max-w-xs truncate">
                                {{ $izin->alasan }}
                            </td>
                            <td class="px-6 py-4">
                                @if($izin->dokumen)
                                    <a href="{{ asset('storage/' . $izin->dokumen) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                                        <i class="fa-solid fa-paperclip"></i> Dokumen Bukti
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center space-x-1">
                                @if($izin->status === 'pending')
                                    <form action="{{ route('admin.absensi.pengajuan.approve', $izin->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition">
                                            <i class="fa-solid fa-check mr-1"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.absensi.pengajuan.reject', $izin->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition">
                                            <i class="fa-solid fa-xmark mr-1"></i> Reject
                                        </button>
                                    </form>
                                @elseif($izin->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                        <i class="fa-solid fa-circle-xmark mr-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-slate-400">
                                Tidak ada pengajuan izin/cuti yang pending.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Daftar Rekap Presensi</h3>
            <span class="text-xs font-medium text-slate-500">Menampilkan {{ $absensiList->count() }} data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#fbfaf8] text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-6 py-3.5">Divisi</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Jenis</th>
                        <th class="px-6 py-3.5">Jam</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($absensiList as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-[#1a2a24] text-white flex items-center justify-center text-xs font-bold uppercase">
                                        {{ substr($item->user->name ?? 'U', 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $item->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-400">{{ $item->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ $item->user->divisi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    <i class="fa-solid {{ $item->jenis === 'masuk' ? 'fa-right-to-bracket' : 'fa-right-from-bracket' }} mr-1.5 text-[10px]"></i>
                                    {{ ucfirst($item->jenis) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-800 font-semibold">
                                {{ substr($item->jam, 0, 5) }} WIB
                            </td>
                            <td class="px-6 py-4">
                                @if($item->status === 'Terlambat')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i> Terlambat
                                    </span>
                                @elseif($item->status === 'Tepat waktu')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-check mr-1 text-[10px]"></i> Tepat Waktu
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $item->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                @php
                                    $displayDetail = preg_replace_callback('/(\d+\.\d+)\s*menit/', function($m) {
                                        return round((float)$m[1]) . ' menit';
                                    }, $item->detail ?? '-');
                                @endphp
                                {{ $displayDetail }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clock text-3xl mb-3 text-slate-300 block"></i>
                                Tidak ada data absensi yang ditemukan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
