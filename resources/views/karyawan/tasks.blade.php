@extends('layouts.app')

@section('title', 'Tugas Saya')
@section('page_title', 'Daftar Tugas Saya')

@section('content')
<div class="space-y-6">
    <!-- View Mode & Filter Bar -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <button type="button" onclick="switchView('list')" id="btn-view-list" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#21352f] text-white shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-list-ul"></i> Tampilan Daftar
            </button>
            <button type="button" onclick="switchView('calendar')" id="btn-view-calendar" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition flex items-center gap-2">
                <i class="fa-solid fa-calendar-days"></i> Tampilan Kalender
            </button>
        </div>

        <form action="{{ route('karyawan.tasks') }}" method="GET" class="flex items-center space-x-2">
            <select name="status" class="rounded-xl border border-slate-300 px-3.5 py-2 bg-white text-xs font-medium text-slate-700 shadow-sm focus:border-[#4d7a6b] focus:outline-none">
                <option value="">Semua Status</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>Dalam Proses</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Menunggu Evaluasi</option>
                <option value="Reviewed" {{ request('status') === 'Reviewed' ? 'selected' : '' }}>Sudah Dievaluasi</option>
            </select>
            <button type="submit" class="bg-[#21352f] text-white font-bold text-xs px-4 py-2 rounded-xl hover:bg-[#1a2a24] shadow transition">
                Filter
            </button>
            @if(request('status'))
                <a href="{{ route('karyawan.tasks') }}" class="text-xs font-semibold text-red-600 hover:text-red-500 underline ml-2">
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
                        <th class="px-6 py-4">Target Pekerjaan</th>
                        <th class="px-6 py-4">Batas Waktu</th>
                        <th class="px-6 py-4">Progres Saat Ini</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $task->judul_tugas }}</div>
                                <div class="text-xs text-slate-400 mt-1 truncate max-w-xs">{{ $task->deskripsi }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $task->target }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium">
                                    {{ \Carbon\Carbon::parse($task->deadline)->format('d F Y') }}
                                </div>
                                <div class="text-xs mt-0.5 {{ \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status !== 'Completed' && $task->status !== 'Reviewed' ? 'text-red-500 font-bold animate-pulse' : 'text-slate-400' }}">
                                    @if($task->status === 'Completed' || $task->status === 'Reviewed')
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
                                    <span class="text-xs text-slate-600 font-bold">{{ $task->persentase }}%</span>
                                </div>
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
            <i class="fa-solid fa-calendar-days text-[#21352f]"></i> Jual Waktu & Batas Waktu Tugas (Calendar View)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($tasks as $task)
                @php
                    $isOverdue = \Carbon\Carbon::parse($task->deadline)->isPast() && !in_array($task->status, ['Completed', 'Reviewed']);
                @endphp
                <div class="p-4 rounded-xl border {{ $isOverdue ? 'border-red-300 bg-red-50/30' : 'border-slate-200 bg-slate-50/50' }} hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-500">
                            <i class="fa-regular fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                        </span>
                        @if($task->status === 'Pending')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-700">Pending</span>
                        @elseif($task->status === 'In Progress')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700">In Progress</span>
                        @elseif($task->status === 'Completed')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">Evaluasi</span>
                        @elseif($task->status === 'Reviewed')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700">Selesai</span>
                        @endif
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm mb-1">{{ $task->judul_tugas }}</h4>
                    <p class="text-xs text-slate-500 mb-3 line-clamp-2">{{ $task->deskripsi }}</p>
                    
                    <div class="space-y-1 mb-3">
                        <div class="flex justify-between text-[11px] text-slate-600 font-medium">
                            <span>Progres</span>
                            <span>{{ $task->persentase }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#21352f] h-1.5 rounded-full" style="width: {{ $task->persentase }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('karyawan.task.detail', $task->id) }}" class="block text-center text-xs font-bold text-[#21352f] hover:text-[#14221e] bg-white py-1.5 border border-slate-200 rounded-lg hover:border-[#21352f] transition">
                        Buka Tugas <i class="fa-solid fa-arrow-right ml-1"></i>
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
