@extends('layouts.app')

@section('title', 'Dashboard Manajer')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome banner with integrated background image -->
    <div class="relative bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 flex items-stretch h-40">
        <div class="p-6 flex-1 flex flex-col justify-center relative z-10 w-1/2">
            <p class="text-slate-500 text-sm font-medium mb-1">Selamat Datang,</p>
            <h2 class="text-2xl font-bold text-slate-800 mb-1.5">{{ auth()->user()->name }}</h2>
            <p class="text-xs text-slate-500 max-w-md">
                Anda memiliki hak akses untuk melakukan penilaian/evaluasi terhadap hasil kerja karyawan.
            </p>
        </div>
        <div class="absolute right-0 top-0 bottom-0 w-2/3">
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
            <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=800&q=80" alt="Office" class="w-full h-full object-cover object-center opacity-90">
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-14 h-14 rounded-full bg-[#f4f2ee] flex items-center justify-center text-[#2d433b]">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Total Karyawan</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalEmployees }}</p>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-14 h-14 rounded-full bg-[#f4f2ee] flex items-center justify-center text-[#2d433b]">
                <i class="fa-solid fa-file-signature text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Tugas Dievaluasi</p>
                <p class="text-2xl font-bold text-slate-800">{{ $reviewedTasks }}</p>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-hourglass-half text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Perlu Evaluasi</p>
                <p class="text-2xl font-bold text-slate-800">{{ $pendingEvaluations->count() }}</p>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-14 h-14 rounded-full bg-[#f4f2ee] flex items-center justify-center text-[#2d433b]">
                <i class="fa-solid fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">KPI Rata-rata</p>
                <p class="text-2xl font-bold text-slate-800">{{ $averageScore }}%</p>
            </div>
        </div>
    </div>

    <!-- Manager Chart Section -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chart-column text-[#4d7a6b]"></i> Performa KPI Karyawan Tertinggi (Top 5)
        </h3>
        <div class="h-64 relative">
            <canvas id="managerLeaderboardChart"></canvas>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxLeaderboard = document.getElementById('managerLeaderboardChart').getContext('2d');
        new Chart(ctxLeaderboard, {
            type: 'bar',
            data: {
                labels: {!! json_encode($employeeScores->pluck('name')) !!},
                datasets: [{
                    label: 'Skor Akhir KPI (%)',
                    data: {!! json_encode($employeeScores->pluck('avg_score')) !!},
                    backgroundColor: '#21352f',
                    borderRadius: 8,
                    barThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    });
</script>

    <!-- Two Columns Layout for Charts/Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tasks Waiting for Evaluation Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Menunggu Evaluasi</h3>
                <a href="{{ route('manajer.tasks') }}" class="text-xs font-semibold text-[#4d7a6b] hover:text-[#2d433b] flex items-center">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#fbfaf8] text-slate-500 font-medium text-xs">
                        <tr>
                            <th class="px-6 py-3 font-medium">Nama Tugas</th>
                            <th class="px-6 py-3 font-medium">Karyawan</th>
                            <th class="px-6 py-3 font-medium">Selesai Pada</th>
                            <th class="px-6 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($pendingEvaluations as $task)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    {{ $task->judul_tugas }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">
                                    {{ $task->karyawan->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    @php
                                        $log = $task->progress()->where('persentase', '>=', 100)->orderBy('tanggal_update', 'asc')->first();
                                        $date = $log ? \Carbon\Carbon::parse($log->tanggal_update) : \Carbon\Carbon::parse($task->updated_at);
                                    @endphp
                                    <span class="flex items-center space-x-1.5 {{ $date->gt(\Carbon\Carbon::parse($task->deadline)) ? 'text-red-500 font-medium' : 'text-slate-700' }}">
                                        <span>{{ $date->format('d M Y') }}</span>
                                        @if($date->gt(\Carbon\Carbon::parse($task->deadline)))
                                            <span class="text-[10px] bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-bold">Terlambat</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('manajer.task.evaluate', $task->id) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#e9eee6] text-[#2d433b] hover:bg-[#d6e3db] transition">
                                        Beri Evaluasi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-sm">Tidak ada tugas menunggu evaluasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
            <h3 class="font-bold text-slate-800 self-start mb-6">Top Kinerja Karyawan</h3>
            <div class="flex-1 space-y-4">
                @php $rank = 1; @endphp
                @forelse($employeeScores->take(4) as $score)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#f4f2ee] flex items-center justify-center font-bold text-sm text-[#2d433b]">
                                {{ $rank++ }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $score->name }}</p>
                                <p class="text-[10px] text-slate-500">{{ $score->tasks_count }} Tugas Selesai</p>
                            </div>
                        </div>
                        <div class="text-sm font-bold text-[#4d7a6b]">{{ $score->avg_score }}%</div>
                    </div>
                @empty
                    <div class="text-center text-slate-400 text-sm py-4">Belum ada rekap data performa.</div>
                @endforelse
            </div>
            @if($employeeScores->count() > 4)
                <a href="{{ route('manajer.performance') }}" class="mt-4 text-center text-xs font-semibold text-[#4d7a6b] hover:text-[#2d433b]">Lihat Peringkat Lengkap</a>
            @endif
        </div>
    </div>
    
    <!-- Bottom Banner -->
    <div class="bg-[#e9eee6] rounded-2xl border border-[#d6e3db] p-6 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-[#2d433b]">
                <i class="fa-solid fa-chart-column text-xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-base">Evaluasi Berkala Tingkatkan Kualitas</h4>
                <p class="text-sm text-slate-600">Pastikan Anda memberikan feedback yang objektif dan membangun kepada tim.</p>
            </div>
        </div>
        <a href="{{ route('manajer.performance') }}" class="bg-[#2d433b] text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-[#1a2a24] transition shadow-sm">
            Lihat Laporan Kinerja <i class="fa-solid fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>
@endsection
