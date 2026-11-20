@extends('layouts.app')

@section('title', 'Kinerja Karyawan')
@section('page_title', 'Daftar Kinerja Karyawan')

@section('content')
<div class="space-y-6">
    @php
        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $labelPeriode = ($bulan && $bulan !== 'all' ? ($namaBulan[$bulan] ?? 'Bulan ' . $bulan) : 'Semua Periode') . ' ' . $tahun;
    @endphp

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <h3 class="font-extrabold text-slate-800 text-base">Peringkat & Ringkasan Kinerja Karyawan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Urutan performa KPI seluruh karyawan untuk periode <strong class="text-slate-800">{{ $labelPeriode }}</strong>.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 no-print self-stretch lg:self-auto">
            <form action="{{ route('manajer.performance') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="bulan" class="rounded-xl border border-slate-300 text-xs font-semibold focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2 px-3 bg-slate-50">
                    <option value="all" {{ $bulan === 'all' ? 'selected' : '' }}>Semua Bulan</option>
                    @foreach($namaBulan as $num => $nama)
                        <option value="{{ $num }}" {{ $bulan === $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>

                <select name="tahun" class="rounded-xl border border-slate-300 text-xs font-semibold focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2 px-3 bg-slate-50">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                @if(!$managerDivisi)
                <select name="divisi" class="rounded-xl border border-slate-300 text-xs font-semibold focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2 px-3 bg-slate-50">
                    <option value="">Semua Divisi</option>
                    @foreach($divisiList as $div)
                        <option value="{{ $div }}" {{ $selectedDivisi === $div ? 'selected' : '' }}>{{ $div }}</option>
                    @endforeach
                </select>
                @endif

                <button type="submit" class="bg-[#21352f] text-white font-bold text-xs px-3.5 py-2 rounded-xl hover:bg-[#1a2a24] shadow transition">
                    Filter
                </button>

                @if($selectedDivisi || request('bulan') || request('tahun'))
                    <a href="{{ route('manajer.performance') }}" class="px-2.5 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition">
                        Reset
                    </a>
                @endif
            </form>
            <button onclick="window.print()" class="bg-[#2d433b] hover:bg-[#1a2a24] text-white font-bold text-xs px-4 py-2 rounded-xl shadow transition flex items-center gap-2 shrink-0">
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

    <!-- Visual Chart per Employee Name -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm no-print">
        <h4 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chart-bar text-[#4d7a6b]"></i> Perbandingan Performa Evaluasi Karyawan (Per Nama)
        </h4>
        <div class="h-56 relative">
            <canvas id="employeePerformanceChart"></canvas>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxPerf = document.getElementById('employeePerformanceChart');
        if (ctxPerf) {
            const names = {!! json_encode($employeeData->pluck('name')) !!};
            const scores = {!! json_encode($employeeData->pluck('avg_score')) !!};
            const colors = ['#21352f', '#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#4b5563'];

            new Chart(ctxPerf.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: names,
                    datasets: [{
                        label: 'Skor Evaluasi Bulanan (%)',
                        data: scores,
                        backgroundColor: '#21352f',
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) { return ' Skor Evaluasi: ' + ctx.parsed.y + '%'; }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100 },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Peringkat</th>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4 text-center">Total Tugas</th>
                        <th class="px-6 py-4 text-center">Selesai (100%)</th>
                        <th class="px-6 py-4 text-center">Dalam Revisi</th>
                        <th class="px-6 py-4 text-center">Skor Akhir KPI</th>
                        <th class="px-6 py-4 text-center">Predikat</th>
                        <th class="px-6 py-4 text-center">Laporan & Aksi</th>
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
                                        <div class="text-xs text-slate-400">{{ $emp->email }} &bull; <span class="font-medium text-slate-500">{{ $emp->divisi ?: '-' }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">
                                {{ $emp->total_tasks }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-emerald-600">
                                {{ $emp->completed_tasks }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold {{ $emp->revisi_tasks > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                {{ $emp->revisi_tasks }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 bg-navy-800 text-white font-extrabold rounded-lg text-sm">
                                    {{ $emp->avg_score }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $emp->avg_score >= 85 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($emp->avg_score >= 70 ? 'bg-blue-50 text-blue-700 border border-blue-200' : ($emp->avg_score >= 55 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-red-700 border border-red-200')) }}">
                                    {{ $emp->category ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('manajer.employee.detail', ['id' => $emp->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-navy-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-file-invoice"></i> Buka Laporan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-4xl mb-2"></i>
                                <p>Belum ada data performa karyawan pada periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
