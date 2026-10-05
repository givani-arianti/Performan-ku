@extends('layouts.app')

@section('title', 'Kinerja Karyawan')
@section('page_title', 'Daftar Kinerja Karyawan')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-800">Ringkasan Kinerja Karyawan</h3>
            <p class="text-xs text-slate-500 mt-1">Daftar ini mengurutkan karyawan berdasarkan rata-rata skor performa secara keseluruhan (dari tertinggi ke terendah).</p>
        </div>
        <div class="flex items-center gap-3 no-print self-stretch sm:self-auto">
            <form action="{{ route('manajer.performance') }}" method="GET" class="flex items-center gap-2">
                <select name="divisi" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-xs focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2 px-3 bg-slate-50 font-semibold">
                    <option value="">-- Semua Divisi --</option>
                    @foreach($divisiList as $div)
                        <option value="{{ $div }}" {{ $selectedDivisi === $div ? 'selected' : '' }}>{{ $div }}</option>
                    @endforeach
                </select>
                @if($selectedDivisi)
                    <a href="{{ route('manajer.performance') }}" class="px-2.5 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition">
                        Reset
                    </a>
                @endif
            </form>
            <button onclick="window.print()" class="bg-[#2d433b] hover:bg-[#1a2a24] text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </button>
        </div>
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
    <h1 class="text-2xl font-bold text-slate-900">LAPORAN RINGKASAN KINERJA KARYAWAN (KPI)</h1>
    <p class="text-sm text-slate-600">Aplikasi Performan-Ku - Tanggal: {{ date('d F Y') }}</p>
</div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Peringkat</th>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4 text-center">Total Tugas</th>
                        <th class="px-6 py-4 text-center">Tugas Diselesaikan</th>
                        <th class="px-6 py-4 text-center">Rata-rata Skor Performa</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $rank = 1; @endphp
                    @forelse($employeeData as $emp)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                #{{ $rank++ }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-full bg-navy-100 text-navy-800 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($emp->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $emp->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $emp->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-slate-600">
                                {{ $emp->total_tasks }}
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-emerald-600">
                                {{ $emp->completed_tasks }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 bg-navy-800 text-white font-bold rounded-lg text-sm">
                                    {{ $emp->avg_score }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('manajer.employee.detail', $emp->id) }}" 
                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-navy-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                    <i class="fa-solid fa-list mr-1.5"></i> Detail Evaluasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-4xl mb-2"></i>
                                <p>Belum ada data performa karyawan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
