@extends('layouts.app')

@section('title', 'Dashboard Karyawan')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome banner with integrated background image -->
    <div class="relative bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 flex items-stretch h-40">
        <div class="p-6 flex-1 flex flex-col justify-center relative z-10 w-1/2">
            <p class="text-slate-500 text-sm font-medium mb-1">Selamat Datang,</p>
            <h2 class="text-2xl font-bold text-slate-800 mb-1.5">{{ auth()->user()->name }}</h2>
            <p class="text-xs text-slate-500 max-w-md">
                Berikut adalah rekap pencapaian performa dan daftar tugas Anda saat ini.
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
                <i class="fa-solid fa-list-check text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Total Tugas</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalTasks }}</p>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-spinner text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Berjalan</p>
                <p class="text-2xl font-bold text-slate-800">{{ $inProgressTasks }}</p>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-14 h-14 rounded-full bg-[#f4f2ee] flex items-center justify-center text-[#2d433b]">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Tugas Selesai</p>
                <p class="text-2xl font-bold text-slate-800">{{ $completedTasks }}</p>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-14 h-14 rounded-full bg-[#f4f2ee] flex items-center justify-center text-[#2d433b]">
                <i class="fa-solid fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Skor Performa</p>
                <p class="text-2xl font-bold text-slate-800">{{ $averageScore }}%</p>
            </div>
        </div>
    </div>

    <!-- Layout for Recent Tasks & Circle Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Tasks -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Daftar Tugas Terdekat</h3>
                <a href="{{ route('karyawan.tasks') }}" class="text-xs font-semibold text-[#4d7a6b] hover:text-[#2d433b] flex items-center">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#fbfaf8] text-slate-500 font-medium text-xs">
                        <tr>
                            <th class="px-6 py-3 font-medium">Judul Tugas</th>
                            <th class="px-6 py-3 font-medium">Deadline</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentTasks->take(5) as $task)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full {{ $task->status === 'Completed' || $task->status === 'Reviewed' ? 'bg-[#4d7a6b]' : ($task->status === 'In Progress' ? 'bg-amber-400' : 'bg-slate-300') }}"></span>
                                    {{ $task->judul_tugas }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <span class="flex items-center space-x-1.5 {{ \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status !== 'Completed' && $task->status !== 'Reviewed' ? 'text-red-600 font-semibold' : '' }}">
                                        <span>{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($task->status === 'Pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Pending</span>
                                    @elseif($task->status === 'In Progress')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">Dalam Proses</span>
                                    @elseif($task->status === 'Completed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#e9eee6] text-[#2d433b]">Evaluasi</span>
                                    @elseif($task->status === 'Reviewed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('karyawan.task.detail', $task->id) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#e9eee6] text-[#2d433b] hover:bg-[#d6e3db] transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-sm">Tidak ada tugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Circle Score -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center">
            <h3 class="font-bold text-slate-800 self-start mb-6">Pencapaian Kinerja</h3>
            <div class="relative flex items-center justify-center mb-6">
                <!-- SVG Circle Progress -->
                <svg class="w-40 h-40 transform -rotate-90">
                    <circle cx="80" cy="80" r="64" stroke="#f4f2ee" stroke-width="16" fill="transparent" />
                    <circle cx="80" cy="80" r="64" stroke="#4d7a6b" stroke-width="16" fill="transparent" 
                        stroke-dasharray="402" 
                        stroke-dashoffset="{{ 402 - (402 * $averageScore) / 100 }}" 
                        class="transition-all duration-1000 ease-out" />
                </svg>
                <div class="absolute text-center flex flex-col items-center">
                    <span class="text-3xl font-extrabold text-slate-800">{{ $averageScore }}<span class="text-lg">%</span></span>
                    <span class="text-[10px] text-slate-500 font-medium">Skor KPI</span>
                </div>
            </div>
            
            <div class="mt-4 text-center w-full">
                @if($averageScore >= 85)
                    <div class="px-4 py-2 text-sm font-semibold text-emerald-800 bg-emerald-100 rounded-lg">Kinerja Sangat Baik!</div>
                @elseif($averageScore >= 70)
                    <div class="px-4 py-2 text-sm font-semibold text-blue-800 bg-blue-100 rounded-lg">Kinerja Sesuai Ekspektasi</div>
                @elseif($averageScore >= 55)
                    <div class="px-4 py-2 text-sm font-semibold text-amber-800 bg-amber-100 rounded-lg">Kinerja Cukup</div>
                @else
                    <div class="px-4 py-2 text-sm font-semibold text-red-800 bg-red-100 rounded-lg">Perlu Peningkatan Kinerja</div>
                @endif
            </div>

            <!-- Radar / Bar Breakdown Chart -->
            <div class="w-full mt-6 pt-4 border-t border-slate-100">
                <p class="text-xs font-semibold text-slate-500 mb-2">Komponen KPI Anda</p>
                <div class="h-44 relative">
                    <canvas id="kpiBreakdownChart"></canvas>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxKpi = document.getElementById('kpiBreakdownChart').getContext('2d');
        new Chart(ctxKpi, {
            type: 'radar',
            data: {
                labels: ['Target', 'Ketepatan Waktu', 'Kualitas', 'Competency', 'Presensi'],
                datasets: [{
                    label: 'Skor (%)',
                    data: [
                        {{ $kpiDetail['target_score'] ?? 0 }},
                        {{ $kpiDetail['timeliness_score'] ?? 0 }},
                        {{ $kpiDetail['quality_score'] ?? 0 }},
                        {{ $kpiDetail['raw_competency'] ?? 0 }},
                        {{ $kpiDetail['attendance_score'] ?? 0 }}
                    ],
                    backgroundColor: 'rgba(77, 122, 107, 0.2)',
                    borderColor: '#4d7a6b',
                    pointBackgroundColor: '#21352f',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#21352f'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        });
    });
</script>

    <!-- Peer comments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800">Beri Komentar Rekan Kerja</h3>
            <p class="text-xs text-slate-500 mt-1 mb-5">Berikan apresiasi atau masukan kepada karyawan lain.</p>
            <form action="{{ route('karyawan.komentar.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="penerima_id" class="block text-xs font-semibold text-slate-600 mb-1">Pilih Karyawan</label>
                    <select id="penerima_id" name="penerima_id" required class="block w-full rounded-lg border border-slate-200 bg-[#fbfaf8] px-3 py-2.5 text-sm text-slate-700 focus:border-[#4d7a6b] focus:outline-none focus:ring-1 focus:ring-[#4d7a6b]">
                        <option value="">Pilih rekan kerja</option>
                        @foreach($rekanKerja as $rekan)
                            <option value="{{ $rekan->id }}" {{ old('penerima_id') == $rekan->id ? 'selected' : '' }}>{{ $rekan->name }}{{ $rekan->divisi ? ' - ' . $rekan->divisi : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="komentar" class="block text-xs font-semibold text-slate-600 mb-1">Komentar</label>
                    <textarea id="komentar" name="komentar" rows="4" required maxlength="1000" class="block w-full rounded-lg border border-slate-200 bg-[#fbfaf8] px-3 py-2.5 text-sm text-slate-700 focus:border-[#4d7a6b] focus:outline-none focus:ring-1 focus:ring-[#4d7a6b]" placeholder="Tulis apresiasi atau masukan...">{{ old('komentar') }}</textarea>
                </div>
                <button type="submit" class="w-full rounded-lg bg-[#2d433b] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1a2a24] transition">
                    Kirim Komentar <i class="fa-solid fa-paper-plane ml-1"></i>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800">Komentar Untuk Anda</h3>
            <p class="text-xs text-slate-500 mt-1 mb-5">Masukan terbaru dari rekan kerja.</p>
            <div class="space-y-3">
                @forelse($komentarDiterima as $komentar)
                    <div class="rounded-xl border border-slate-100 bg-[#fbfaf8] p-4">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <span class="text-xs font-semibold text-slate-800 flex items-center">
                                <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center mr-2"><i class="fa-solid fa-user text-[10px]"></i></div> 
                                Rekan Kerja Anonim
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $komentar->created_at->format('d M Y') }}</span>
                        </div>
                        <p class="text-sm text-slate-600 whitespace-pre-line">{{ $komentar->komentar }}</p>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-slate-400">Belum ada komentar dari rekan kerja.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
