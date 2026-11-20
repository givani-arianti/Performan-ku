@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-4">
    <!-- Welcome banner -->
    <div class="relative bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 flex items-stretch h-24">
        <!-- Text Area -->
        <div class="px-5 flex-1 flex flex-col justify-center relative z-10 w-1/2">
            <p class="text-slate-500 text-xs font-medium">Selamat Datang,</p>
            <h2 class="text-lg font-bold text-slate-800">{{ auth()->user()->name }}</h2>
            <p class="text-xs text-slate-400">Ringkasan performa karyawan & progres tugas.</p>
        </div>
        <!-- Image Area -->
        <div class="absolute right-0 top-0 bottom-0 w-2/3">
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
            <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=800&q=80" alt="Office" class="w-full h-full object-cover object-center opacity-90">
        </div>
    </div>

    @if($topEmployee)
    <!-- Employee of the Month Banner -->
    <div class="bg-gradient-to-r from-[#1a2a24] via-[#21352f] to-[#2a423a] rounded-xl p-4 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-4 relative overflow-hidden">
        <div class="flex items-center space-x-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center font-bold text-2xl shadow-lg shrink-0">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <div class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-400/20 text-amber-300 border border-amber-400/30 mb-0.5">
                    <i class="fa-solid fa-star mr-1 text-amber-400"></i> Karyawan Terbaik Bulan Ini
                </div>
                <h3 class="text-base font-extrabold text-white">{{ $topEmployee->name }}</h3>
                <p class="text-xs text-slate-300">{{ $topEmployee->divisi ? 'Divisi ' . $topEmployee->divisi . ' • ' : '' }}Pencapaian KPI Tertinggi</p>
            </div>
        </div>
        <div class="flex items-center gap-3 relative z-10 self-end md:self-auto">
            <div class="text-right">
                <p class="text-xs text-slate-400">Skor KPI Akhir</p>
                <p class="text-2xl font-black text-amber-400">{{ $topEmployee->score }}%</p>
            </div>
            <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">{{ $topEmployee->category }}</span>
        </div>
    </div>
    @endif

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Card 1 -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-[#f4f2ee] flex items-center justify-center text-[#2d433b] shrink-0">
                <i class="fa-solid fa-user-group text-base"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Karyawan</p>
                <p class="text-xl font-bold text-slate-800">{{ $totalEmployees }}</p>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-[#f4f2ee] flex items-center justify-center text-[#2d433b] shrink-0">
                <i class="fa-solid fa-clipboard-list text-base"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Tugas</p>
                <p class="text-xl font-bold text-slate-800">{{ $totalTasks }}</p>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-[#f4f2ee] flex items-center justify-center text-[#2d433b] shrink-0">
                <i class="fa-solid fa-circle-check text-base"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Tugas Selesai</p>
                <p class="text-xl font-bold text-slate-800">{{ $completedTasks + $reviewedTasks }}</p>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-[#f4f2ee] flex items-center justify-center text-[#2d433b] shrink-0">
                <i class="fa-solid fa-chart-line text-base"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Rata-rata Performa</p>
                <p class="text-xl font-bold text-slate-800">{{ $averageScore }}%</p>
            </div>
        </div>
    </div>

    <!-- Visual Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
            <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-[#4d7a6b]"></i> Distribusi Status Tugas
            </h3>
            <div class="h-52 relative flex items-center justify-center">
                <canvas id="taskStatusChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
            <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                <i class="fa-solid fa-chart-bar text-[#4d7a6b]"></i> Kehadiran Karyawan Hari Ini
            </h3>
            <div class="h-52 relative flex items-center justify-center">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Task Status Chart
        const ctxTask = document.getElementById('taskStatusChart').getContext('2d');
        new Chart(ctxTask, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed', 'Reviewed'],
                datasets: [{
                    data: [{{ $pendingTasks }}, {{ $inProgressTasks }}, {{ $completedTasks }}, {{ $reviewedTasks }}],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#6366f1'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Attendance Chart
        const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctxAttendance, {
            type: 'bar',
            data: {
                labels: ['Tepat Waktu', 'Terlambat', 'Belum Absen'],
                datasets: [{
                    label: 'Jumlah Karyawan',
                    data: [{{ $onTimeCount }}, {{ $lateCount }}, {{ $notCheckedInCount }}],
                    backgroundColor: ['#10b981', '#ef4444', '#94a3b8'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>

    <!-- Attendance Today & Recent Logs Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fa-solid fa-clock text-[#4d7a6b]"></i> Presensi Karyawan Hari Ini
            </h3>
            <a href="{{ route('admin.absensi') }}" class="text-xs font-semibold text-[#4d7a6b] hover:text-[#2d433b] flex items-center">
                Lihat Rekap Lengkap <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-medium">Hadir Hari Ini</p>
                    <p class="text-lg font-bold text-slate-800 mt-0.5">{{ $presentCount }} <span class="text-xs font-normal text-slate-500">/ {{ $totalEmployees }}</span></p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-user-check text-sm"></i>
                </div>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-medium">Tepat Waktu</p>
                    <p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $onTimeCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-medium">Terlambat</p>
                    <p class="text-lg font-bold text-red-600 mt-0.5">{{ $lateCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-medium">Belum Absen</p>
                    <p class="text-lg font-bold text-slate-600 mt-0.5">{{ $notCheckedInCount }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                    <i class="fa-solid fa-user-clock text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Columns Layout for Tasks & Recent Absensi Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Tasks Table -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-3.5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-800">Tugas yang Baru Dibuat</h3>
                <a href="{{ route('admin.monitoring') }}" class="text-xs font-semibold text-[#4d7a6b] hover:text-[#2d433b] flex items-center">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#fbfaf8] text-slate-500 font-medium text-xs">
                        <tr>
                            <th class="px-5 py-2.5 font-medium">Nama Tugas</th>
                            <th class="px-5 py-2.5 font-medium">Karyawan</th>
                            <th class="px-5 py-2.5 font-medium">Deadline</th>
                            <th class="px-5 py-2.5 font-medium text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentTasks->take(5) as $task)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-medium text-slate-800 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $task->status === 'Completed' || $task->status === 'Reviewed' ? 'bg-[#4d7a6b]' : ($task->status === 'In Progress' ? 'bg-amber-400' : 'bg-slate-300') }} shrink-0"></span>
                                    {{ $task->judul_tugas }}
                                </td>
                                <td class="px-5 py-3 text-slate-600 font-medium">{{ $task->karyawan->name }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-right">
                                    @if($task->status === 'Pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Pending</span>
                                    @elseif($task->status === 'In Progress')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">Dalam Proses</span>
                                    @elseif($task->status === 'Completed' || $task->status === 'Reviewed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-slate-400 text-sm">Belum ada tugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pratinjau Log Absensi Terbaru -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-3.5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-800">Log Absensi Terbaru</h3>
                <a href="{{ route('admin.absensi') }}" class="text-xs font-semibold text-[#4d7a6b] hover:text-[#2d433b]">Detail</a>
            </div>
            <div class="p-3 flex-1 space-y-2">
                @forelse($recentAbsensi as $abs)
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-7 h-7 rounded-full bg-[#21352f] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                {{ substr($abs->user->name ?? 'U', 0, 2) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ $abs->user->name ?? 'Karyawan' }}</p>
                                <p class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($abs->tanggal)->format('d M Y') }} • {{ substr($abs->jam, 0, 5) }} WIB</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $abs->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                {{ ucfirst($abs->jenis) }}
                            </span>
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $abs->status }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Belum ada data absensi tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Bottom Banner -->
    <div class="bg-[#e9eee6] rounded-xl border border-[#d6e3db] p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#2d433b] shrink-0">
                <i class="fa-regular fa-lightbulb text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">Tingkatkan Performa Tim Anda</h4>
                <p class="text-xs text-slate-600">Gunakan data analitik untuk membuat keputusan yang lebih baik.</p>
            </div>
        </div>
        <a href="{{ route('admin.monitoring') }}" class="bg-[#2d433b] text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-[#1a2a24] transition shadow-sm whitespace-nowrap">
            Lihat Laporan <i class="fa-solid fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>
@endsection
