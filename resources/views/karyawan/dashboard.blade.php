@extends('layouts.app')

@section('title', 'Dashboard Karyawan')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-4">
    <!-- Welcome banner -->
    <div class="relative bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 flex items-stretch h-24">
        <div class="px-5 flex-1 flex flex-col justify-center relative z-10 w-1/2">
            <p class="text-slate-500 text-xs font-medium">Selamat Datang,</p>
            <h2 class="text-lg font-bold text-slate-800">{{ auth()->user()->name }}</h2>
            <p class="text-xs text-slate-400 max-w-md">Rekap performa dan daftar tugas Anda.</p>
        </div>
        <div class="absolute right-0 top-0 bottom-0 w-2/3">
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
            <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=800&q=80" alt="Office" class="w-full h-full object-cover object-center opacity-90">
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Card 1 -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-[#f4f2ee] flex items-center justify-center text-[#2d433b] shrink-0">
                <i class="fa-solid fa-list-check text-base"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Tugas</p>
                <p class="text-xl font-bold text-slate-800">{{ $totalTasks }}</p>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                <i class="fa-solid fa-spinner text-base"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Berjalan</p>
                <p class="text-xl font-bold text-slate-800">{{ $inProgressTasks }}</p>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-[#f4f2ee] flex items-center justify-center text-[#2d433b] shrink-0">
                <i class="fa-solid fa-circle-check text-base"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Tugas Selesai</p>
                <p class="text-xl font-bold text-slate-800">{{ $completedTasks }}</p>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg {{ $revisiTasks > 0 ? 'bg-amber-100 text-amber-800' : 'bg-[#f4f2ee] text-[#2d433b]' }} flex items-center justify-center shrink-0">
                <i class="fa-solid {{ $revisiTasks > 0 ? 'fa-triangle-exclamation' : 'fa-chart-line' }} text-base"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">{{ $revisiTasks > 0 ? 'Perlu Revisi' : 'Skor Performa' }}</p>
                <p class="text-xl font-bold {{ $revisiTasks > 0 ? 'text-amber-800' : 'text-slate-800' }}">
                    {{ $revisiTasks > 0 ? $revisiTasks . ' Tugas' : $averageScore . '%' }}
                </p>
            </div>
        </div>
    </div>

    @if($revisiTasks > 0)
        <!-- Urgent Revision Alert -->
        <div class="p-3 rounded-xl bg-amber-50 border border-amber-300 flex items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-200 text-amber-800 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-amber-900">Ada {{ $revisiTasks }} Tugas Memerlukan Revisi!</h4>
                    <p class="text-xs text-amber-800">Manajer telah mengirimkan catatan perbaikan. Segera cek dan kirimkan hasil revisi.</p>
                </div>
            </div>
            <a href="{{ route('karyawan.tasks', ['status' => 'Revisi']) }}" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow-sm transition whitespace-nowrap">
                Buka &rarr;
            </a>
        </div>
    @endif

    <!-- Layout for Recent Tasks & Circle Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Tasks -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-3.5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-800">Daftar Tugas Terdekat</h3>
                <a href="{{ route('karyawan.tasks') }}" class="text-xs font-semibold text-[#4d7a6b] hover:text-[#2d433b] flex items-center">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#fbfaf8] text-slate-500 font-medium text-xs">
                        <tr>
                            <th class="px-5 py-2.5 font-medium">Judul Tugas</th>
                            <th class="px-5 py-2.5 font-medium">Deadline</th>
                            <th class="px-5 py-2.5 font-medium">Status</th>
                            <th class="px-5 py-2.5 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentTasks->take(5) as $task)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-medium text-slate-800 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $task->status === 'Completed' || $task->status === 'Reviewed' ? 'bg-[#4d7a6b]' : ($task->status === 'In Progress' ? 'bg-amber-400' : 'bg-slate-300') }} shrink-0"></span>
                                    {{ $task->judul_tugas }}
                                </td>
                                <td class="px-5 py-3 text-slate-600">
                                    <span class="flex items-center space-x-1.5 {{ \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status !== 'Completed' && $task->status !== 'Reviewed' ? 'text-red-600 font-semibold' : '' }}">
                                        <span>{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}</span>
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    @if($task->status === 'Pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Pending</span>
                                    @elseif($task->status === 'In Progress')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600">Dalam Proses</span>
                                    @elseif($task->status === 'Revisi')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">Revisi</span>
                                    @elseif($task->status === 'Completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#e9eee6] text-[#2d433b]">Evaluasi</span>
                                    @elseif($task->status === 'Reviewed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('karyawan.task.detail', $task->id) }}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[#e9eee6] text-[#2d433b] hover:bg-[#d6e3db] transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-slate-400 text-sm">Tidak ada tugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Circle Score -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex flex-col items-center">
            <h3 class="font-bold text-sm text-slate-800 self-start mb-4">Pencapaian Kinerja</h3>
            <div class="relative flex items-center justify-center mb-4">
                <svg class="w-32 h-32 transform -rotate-90">
                    <circle cx="64" cy="64" r="52" stroke="#f4f2ee" stroke-width="12" fill="transparent" />
                    <circle cx="64" cy="64" r="52" stroke="#4d7a6b" stroke-width="12" fill="transparent" 
                        stroke-dasharray="327" 
                        stroke-dashoffset="{{ 327 - (327 * $averageScore) / 100 }}" 
                        class="transition-all duration-1000 ease-out" />
                </svg>
                <div class="absolute text-center flex flex-col items-center">
                    <span class="text-2xl font-extrabold text-slate-800">{{ $averageScore }}<span class="text-sm">%</span></span>
                    <span class="text-[10px] text-slate-500 font-medium">Skor KPI</span>
                </div>
            </div>
            
            <div class="text-center w-full mb-4">
                @if($averageScore >= 85)
                    <div class="px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-100 rounded-lg">Kinerja Sangat Baik!</div>
                @elseif($averageScore >= 70)
                    <div class="px-3 py-1.5 text-xs font-semibold text-blue-800 bg-blue-100 rounded-lg">Kinerja Sesuai Ekspektasi</div>
                @elseif($averageScore >= 55)
                    <div class="px-3 py-1.5 text-xs font-semibold text-amber-800 bg-amber-100 rounded-lg">Kinerja Cukup</div>
                @else
                    <div class="px-3 py-1.5 text-xs font-semibold text-red-800 bg-red-100 rounded-lg">Perlu Peningkatan Kinerja</div>
                @endif
            </div>

            <!-- Component Breakdown -->
            <div class="w-full pt-3 border-t border-slate-100 space-y-2">
                <p class="text-xs font-bold text-slate-700 mb-1.5">Hasil Evaluasi Bulan Ini</p>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-600"><i class="fa-solid fa-list-check mr-1.5"></i>Banyak Tugas Selesai</span>
                    <span class="font-extrabold text-slate-800">{{ $kpiDetail['target_score'] ?? 0 }}%</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-600"><i class="fa-solid fa-clock text-blue-600 mr-1.5"></i>Ketepatan Waktu</span>
                    <span class="font-extrabold text-slate-800">{{ $kpiDetail['timeliness_score'] ?? 0 }}%</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-600"><i class="fa-solid fa-gauge-high text-emerald-600 mr-1.5"></i>Kecepatan Pengerjaan</span>
                    <span class="font-extrabold text-slate-800">{{ $kpiDetail['speed_score'] ?? 0 }}%</span>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxKpi = document.getElementById('kpiBreakdownChart');
        if (ctxKpi) {
            new Chart(ctxKpi.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Banyak Selesai', 'Ketepatan Waktu', 'Kecepatan Sesuai DL'],
                    datasets: [{
                        label: 'Skor Evaluasi Bulanan (%)',
                        data: [
                            {{ $kpiDetail['target_score'] ?? 0 }},
                            {{ $kpiDetail['timeliness_score'] ?? 0 }},
                            {{ $kpiDetail['speed_score'] ?? 0 }}
                        ],
                        backgroundColor: ['#21352f', '#3b82f6', '#10b981'],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    });
</script>

</div>
@endsection
