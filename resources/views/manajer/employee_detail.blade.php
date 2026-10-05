@extends('layouts.app')

@section('title', 'Detail Kinerja Karyawan')
@section('page_title', 'Detail Kinerja Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Back to performance list -->
    <div>
        <a href="{{ route('manajer.performance') }}" class="inline-flex items-center text-sm font-semibold text-navy-800 hover:text-navy-600">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Kembali ke Daftar Kinerja
        </a>
    </div>

    <!-- Employee Info -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-navy-100 border-2 border-navy-800 text-navy-800 flex items-center justify-center font-bold text-xl uppercase">
                {{ substr($employee->name, 0, 2) }}
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">{{ $employee->name }}</h3>
                <p class="text-sm text-slate-500">{{ $employee->email }}</p>
                <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                    Karyawan
                </span>
            </div>
        </div>
        
        @php
            $tasks = $employee->assignedTasks;
            $totalTasks = $tasks->count();
            $completedTasks = $tasks->whereIn('status', ['Completed', 'Reviewed'])->count();
            $kpi = $employee->calculatePerformanceKpi($tasks);
            $avgScore = $kpi['final_score'];
        @endphp

        <div class="flex items-center space-x-6 bg-slate-50 p-4 rounded-xl border border-slate-100 w-full md:w-auto">
            <div class="text-center">
                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-1">Tugas Selesai</p>
                <p class="text-xl font-bold text-slate-800">{{ $completedTasks }} / {{ $totalTasks }}</p>
            </div>
            <div class="w-px h-10 bg-slate-200"></div>
            <div class="text-center">
                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-1">Skor Performa</p>
                <p class="text-xl font-extrabold text-navy-800">{{ $avgScore }}%</p>
                <p class="text-[10px] text-slate-400 mt-1">{{ $kpi['category'] }}</p>
            </div>
        </div>
    </div>

    <!-- Tasks List for this Employee -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h3 class="font-bold text-slate-800">Daftar Tugas & Evaluasi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Nama Tugas</th>
                        <th class="px-6 py-3.5">Batas Waktu</th>
                        <th class="px-6 py-3.5">Progres</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-center">Evaluasi / Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employee->assignedTasks as $task)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $task->judul_tugas }}</div>
                                <div class="text-[10px] text-slate-400 mt-1 truncate max-w-xs">{{ $task->target }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-16 bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-navy-800 h-1.5 rounded-full" style="width: {{ $task->persentase }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 font-bold">{{ $task->persentase }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->status === 'Pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-800">Pending</span>
                                @elseif($task->status === 'In Progress')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700">Dalam Proses</span>
                                @elseif($task->status === 'Completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">Menunggu Evaluasi</span>
                                @elseif($task->status === 'Reviewed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">Sudah Dievaluasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($task->status === 'Completed')
                                    <a href="{{ route('manajer.task.evaluate', $task->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-[10px] font-bold text-white bg-purple-700 hover:bg-purple-800 rounded shadow-sm transition">
                                        Evaluasi Sekarang
                                    </a>
                                @elseif($task->status === 'Reviewed')
                                    <a href="{{ route('manajer.task.evaluate', $task->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-[10px] font-bold text-navy-800 bg-slate-100 hover:bg-slate-200 rounded transition">
                                        Lihat Evaluasi ({{ $task->evaluasi->score }})
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Belum selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <p>Karyawan ini belum diberikan tugas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
