@extends('layouts.app')

@section('title', 'Monitoring Kinerja')
@section('page_title', 'Monitoring Kinerja Karyawan')

@section('content')
<div class="space-y-6">

    <!-- Division Filter Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-building text-[#4d7a6b]"></i> Filter Berdasarkan Divisi / Departemen
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Tampilkan data kinerja dan daftar tugas karyawan berdasarkan divisi tertentu.</p>
        </div>
        <form action="{{ route('admin.monitoring') }}" method="GET" class="flex items-center gap-2 self-stretch sm:self-auto">
            <select name="divisi" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2 px-3 bg-slate-50 font-medium">
                <option value="">-- Semua Divisi --</option>
                @foreach($divisiList as $div)
                    <option value="{{ $div }}" {{ $selectedDivisi === $div ? 'selected' : '' }}>{{ $div }}</option>
                @endforeach
            </select>
            @if($selectedDivisi)
                <a href="{{ route('admin.monitoring') }}" class="px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Employee Performance Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Rekap Performa Karyawan</h3>
                <p class="text-xs text-slate-500 mt-0.5">Data dihitung dari seluruh tugas yang telah didelegasikan oleh Manajer.</p>
            </div>
            @if($selectedDivisi)
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                    Divisi: {{ $selectedDivisi }}
                </span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Peringkat</th>
                        <th class="px-6 py-3.5">Nama Karyawan</th>
                        <th class="px-6 py-3.5">Total Tugas</th>
                        <th class="px-6 py-3.5">Selesai</th>
                        <th class="px-6 py-3.5">Skor Rata-rata</th>
                        <th class="px-6 py-3.5">Status Kinerja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employeeData as $index => $emp)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                @if($index + 1 === 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 text-yellow-700 font-bold text-sm">1</span>
                                @elseif($index + 1 === 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-slate-600 font-bold text-sm">2</span>
                                @elseif($index + 1 === 3)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-700 font-bold text-sm">3</span>
                                @else
                                    <span class="text-slate-500 font-semibold">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800 flex items-center gap-2">
                                    {{ $emp->name }}
                                    @if($emp->divisi)
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 bg-blue-50 border border-blue-100 text-blue-600 rounded">
                                            {{ $emp->divisi }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-400">{{ $emp->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ $emp->total_tasks }}</td>
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ $emp->completed_tasks }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 bg-slate-200 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full {{ $emp->avg_score >= 85 ? 'bg-emerald-500' : ($emp->avg_score >= 70 ? 'bg-blue-500' : 'bg-red-400') }}" 
                                             style="width: {{ $emp->avg_score }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">{{ $emp->avg_score }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($emp->avg_score >= 85)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Sangat Baik
                                    </span>
                                @elseif($emp->avg_score >= 70)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span> Baik
                                    </span>
                                @elseif($emp->avg_score >= 55)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Perlu Peningkatan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        Belum Ada Tugas
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-4xl mb-2"></i>
                                <p>Belum ada data karyawan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Tasks Overview (Read Only) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Daftar Semua Tugas</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tugas yang telah didelegasikan oleh Manajer kepada Karyawan.</p>
            </div>
            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-lg font-medium">
                <i class="fa-solid fa-eye mr-1"></i> Hanya Lihat
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Judul Tugas</th>
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-6 py-3.5">Target</th>
                        <th class="px-6 py-3.5">Deadline</th>
                        <th class="px-6 py-3.5">Progres</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $task->judul_tugas }}</div>
                                @if($task->divisi)
                                    <div class="inline-flex mt-1 mb-0.5 px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100">{{ $task->divisi }}</div>
                                @endif
                                <div class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">{{ $task->deskripsi }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $task->karyawan->name }}</div>
                                <div class="text-xs text-slate-400">{{ $task->karyawan->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $task->target }}</td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium">{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}</div>
                                <div class="text-xs {{ \Carbon\Carbon::parse($task->deadline)->isPast() && !in_array($task->status, ['Completed','Reviewed']) ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                    @if(in_array($task->status, ['Completed', 'Reviewed']))
                                        Selesai
                                    @else
                                        {{ \Carbon\Carbon::parse($task->deadline)->diffForHumans() }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 bg-slate-200 rounded-full h-1.5">
                                        <div class="bg-navy-800 h-1.5 rounded-full" style="width: {{ $task->persentase }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500">{{ $task->persentase }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->status === 'Pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">Pending</span>
                                @elseif($task->status === 'In Progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">Dalam Proses</span>
                                @elseif($task->status === 'Completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">Menunggu Evaluasi</span>
                                @elseif($task->status === 'Reviewed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Dievaluasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-2"></i>
                                <p>Belum ada tugas yang dibuat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
