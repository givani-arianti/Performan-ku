@extends('layouts.app')

@section('title', 'Tugas Saya')
@section('page_title', 'Daftar Tugas Saya')

@section('content')
<div class="space-y-6">
    <!-- Monthly Summary Cards (Rekapan Tugas Karyawan Per Bulan) -->
    @php
        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $labelPeriode = ($bulan && $bulan !== 'all' ? ($namaBulan[$bulan] ?? 'Bulan ' . $bulan) : 'Semua Bulan') . ' ' . $tahun;
    @endphp

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-[#21352f]"></i>
                    Rekapan Tugas Bulanan Saya ({{ $labelPeriode }})
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Statistik ringkasan akumulasi progres dan pencapaian target tugas Anda.</p>
            </div>
            <a href="{{ route('karyawan.laporan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-[#21352f] hover:bg-[#1a2a24] shadow-sm transition shrink-0">
                <i class="fa-solid fa-file-invoice"></i> Lihat & Cetak Laporan Kinerja
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3.5">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Tugas</span>
                <span class="text-2xl font-extrabold text-slate-800">{{ $monthlySummary['total'] ?? 0 }}</span>
                <p class="text-[10px] text-slate-500 mt-0.5">Periode {{ $labelPeriode }}</p>
            </div>
            <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-100">
                <span class="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-1">Selesai (100%)</span>
                <span class="text-2xl font-extrabold text-emerald-700">{{ $monthlySummary['completed'] ?? 0 }}</span>
                <p class="text-[10px] text-emerald-600 mt-0.5">Selesai / Dievaluasi</p>
            </div>
            <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200">
                <span class="block text-[10px] font-bold text-amber-800 uppercase tracking-wider mb-1">Perlu Revisi</span>
                <span class="text-2xl font-extrabold text-amber-800">{{ $monthlySummary['revisi'] ?? 0 }}</span>
                <p class="text-[10px] text-amber-700 mt-0.5">Perlu perbaikan segera</p>
            </div>
            <div class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-100">
                <span class="block text-[10px] font-bold text-indigo-700 uppercase tracking-wider mb-1">Dalam Proses</span>
                <span class="text-2xl font-extrabold text-indigo-700">{{ $monthlySummary['in_progress'] ?? 0 }}</span>
                <p class="text-[10px] text-indigo-600 mt-0.5">Pending / Berjalan</p>
            </div>
            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100 col-span-2 sm:col-span-1">
                <span class="block text-[10px] font-bold text-blue-700 uppercase tracking-wider mb-1">Tepat Waktu</span>
                <span class="text-2xl font-extrabold text-blue-800">{{ $monthlySummary['on_time'] ?? 0 }}</span>
                <p class="text-[10px] text-blue-600 mt-0.5">Sesuai tenggat DL</p>
            </div>
        </div>
    </div>

    <!-- View Mode & Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <button type="button" onclick="switchView('list')" id="btn-view-list" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#21352f] text-white shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-list-ul"></i> Tampilan Daftar
            </button>
            <button type="button" onclick="switchView('calendar')" id="btn-view-calendar" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition flex items-center gap-2">
                <i class="fa-solid fa-calendar-days"></i> Tampilan Kalender
            </button>
        </div>

        <form action="{{ route('karyawan.tasks') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <!-- Filter Bulan -->
            <select name="bulan" class="rounded-xl border border-slate-300 px-3 py-2 bg-white text-xs font-semibold text-slate-700 shadow-sm focus:border-[#4d7a6b] focus:outline-none">
                <option value="all" {{ $bulan === 'all' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach($namaBulan as $num => $nama)
                    <option value="{{ $num }}" {{ $bulan === $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>

            <!-- Filter Tahun -->
            <select name="tahun" class="rounded-xl border border-slate-300 px-3 py-2 bg-white text-xs font-semibold text-slate-700 shadow-sm focus:border-[#4d7a6b] focus:outline-none">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <!-- Filter Status -->
            <select name="status" class="rounded-xl border border-slate-300 px-3 py-2 bg-white text-xs font-semibold text-slate-700 shadow-sm focus:border-[#4d7a6b] focus:outline-none">
                <option value="">Semua Status</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>Dalam Proses</option>
                <option value="Revisi" {{ request('status') === 'Revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Menunggu Evaluasi</option>
                <option value="Reviewed" {{ request('status') === 'Reviewed' ? 'selected' : '' }}>Sudah Dievaluasi</option>
            </select>

            <button type="submit" class="bg-[#21352f] text-white font-bold text-xs px-4 py-2 rounded-xl hover:bg-[#1a2a24] shadow transition">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            @if(request('status') || request('bulan') || request('tahun'))
                <a href="{{ route('karyawan.tasks') }}" class="text-xs font-semibold text-red-600 hover:text-red-500 underline ml-1">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Task List View -->
    <div id="list-view" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Judul Tugas</th>
                        <th class="px-6 py-4">Batas Waktu (DL)</th>
                        <th class="px-6 py-4">% Pengerjaan Tugas</th>
                        <th class="px-6 py-4">Kecepatan Sesuai DL</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tasks as $task)
                        @php $kecepatan = $task->kecepatan_info; @endphp
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $task->judul_tugas }}</div>
                                <div class="text-xs text-slate-400 mt-1 truncate max-w-xs">{{ $task->deskripsi }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium">
                                    {{ \Carbon\Carbon::parse($task->deadline)->format('d F Y') }}
                                </div>
                                <div class="text-xs mt-0.5 {{ \Carbon\Carbon::parse($task->deadline)->isPast() && !in_array($task->status, ['Completed', 'Reviewed']) ? 'text-red-500 font-bold animate-pulse' : 'text-slate-400' }}">
                                    @if(in_array($task->status, ['Completed', 'Reviewed']))
                                        Selesai
                                    @else
                                        {{ \Carbon\Carbon::parse($task->deadline)->diffForHumans() }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-28 bg-slate-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-navy-600 h-2 rounded-full" style="width: {{ $task->persentase }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-700 font-extrabold">{{ $task->persentase }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $kecepatan['color'] }} border">
                                    <i class="fa-solid fa-gauge-high mr-1.5 text-[10px]"></i>
                                    {{ $kecepatan['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->status === 'Pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                        Pending
                                    </span>
                                @elseif($task->status === 'In Progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                        Dalam Proses
                                    </span>
                                @elseif($task->status === 'Revisi')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        <i class="fa-solid fa-arrows-rotate mr-1 text-[10px]"></i> Perlu Revisi
                                    </span>
                                @elseif($task->status === 'Completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                        Menunggu Evaluasi
                                    </span>
                                @elseif($task->status === 'Reviewed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        Sudah Dievaluasi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('karyawan.task.detail', $task->id) }}" 
                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-white bg-[#21352f] rounded-lg hover:bg-[#1a2a24] shadow-sm transition">
                                    Detail & Checklist
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-2"></i>
                                <p>Tidak ditemukan tugas yang sesuai dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Task Calendar View -->
    <div id="calendar-view" class="hidden bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days text-[#21352f]"></i> Jadwal Waktu & Batas Waktu Tugas (Calendar View)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($tasks as $task)
                @php
                    $isOverdue = \Carbon\Carbon::parse($task->deadline)->isPast() && !in_array($task->status, ['Completed', 'Reviewed']);
                    $kecepatan = $task->kecepatan_info;
                @endphp
                <div class="p-4 rounded-xl border {{ $isOverdue ? 'border-red-300 bg-red-50/30' : 'border-slate-200 bg-slate-50/50' }} hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-500">
                            <i class="fa-regular fa-clock mr-1"></i>DL: {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                        </span>
                        @if($task->status === 'Pending')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-700">Pending</span>
                        @elseif($task->status === 'In Progress')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700">In Progress</span>
                        @elseif($task->status === 'Revisi')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-300">Revisi</span>
                        @elseif($task->status === 'Completed')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">Evaluasi</span>
                        @elseif($task->status === 'Reviewed')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700">Selesai</span>
                        @endif
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm mb-1">{{ $task->judul_tugas }}</h4>
                    <p class="text-xs text-slate-500 mb-3 line-clamp-2">{{ $task->deskripsi }}</p>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between text-[11px] text-slate-600 font-medium">
                            <span>Progres Pengerjaan</span>
                            <span class="font-bold text-slate-800">{{ $task->persentase }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#21352f] h-1.5 rounded-full" style="width: {{ $task->persentase }}%"></div>
                        </div>
                        <div class="pt-1">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $kecepatan['color'] }} border">
                                {{ $kecepatan['label'] }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('karyawan.task.detail', $task->id) }}" class="block text-center text-xs font-bold text-[#21352f] hover:text-[#14221e] bg-white py-1.5 border border-slate-200 rounded-lg hover:border-[#21352f] transition">
                        Buka Detail <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    <i class="fa-solid fa-calendar-xmark text-4xl mb-2"></i>
                    <p>Tidak ada tugas untuk ditampilkan di kalender.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function switchView(mode) {
        const listView = document.getElementById('list-view');
        const calendarView = document.getElementById('calendar-view');
        const btnList = document.getElementById('btn-view-list');
        const btnCalendar = document.getElementById('btn-view-calendar');

        if (mode === 'calendar') {
            listView.classList.add('hidden');
            calendarView.classList.remove('hidden');

            btnCalendar.className = "px-4 py-2 rounded-xl text-xs font-bold bg-[#21352f] text-white shadow-sm transition flex items-center gap-2";
            btnList.className = "px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition flex items-center gap-2";
        } else {
            calendarView.classList.add('hidden');
            listView.classList.remove('hidden');

            btnList.className = "px-4 py-2 rounded-xl text-xs font-bold bg-[#21352f] text-white shadow-sm transition flex items-center gap-2";
            btnCalendar.className = "px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition flex items-center gap-2";
        }
    }
</script>
@endsection
