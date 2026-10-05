@extends('layouts.app')

@section('title', 'Riwayat Evaluasi')
@section('page_title', 'Riwayat Evaluasi Kinerja')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="font-bold text-slate-800">Riwayat Penilaian</h3>
            <p class="text-xs text-slate-500 mt-1">Daftar semua tugas karyawan yang telah Anda evaluasi.</p>
        </div>
        <div class="text-right">
            <span class="text-2xl font-extrabold text-navy-800">{{ $evaluations->count() }}</span>
            <p class="text-xs text-slate-500">Total Evaluasi</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">Tugas</th>
                        <th class="px-6 py-4 text-center">Tgl Evaluasi</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Skor</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($evaluations as $evaluasi)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $evaluasi->task->karyawan->name ?? '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $evaluasi->task->karyawan->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $evaluasi->task->judul_tugas ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[200px]">{{ $evaluasi->task->target ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-600 text-xs">
                                {{ $evaluasi->tanggal_evaluasi ? \Carbon\Carbon::parse($evaluasi->tanggal_evaluasi)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($evaluasi->status_evaluasi === 'Sesuai Ekspektasi')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Sesuai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                        Belum Sesuai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $score = $evaluasi->score;
                                    $scoreColor = $score >= 80 ? 'bg-emerald-100 text-emerald-800' : ($score >= 60 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800');
                                @endphp
                                <span class="inline-flex items-center justify-center w-12 h-8 {{ $scoreColor }} font-bold rounded-lg text-sm">
                                    {{ $score }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($evaluasi->task)
                                    <a href="{{ route('manajer.task.evaluate', $evaluasi->task->id) }}"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-[10px] font-bold text-navy-800 bg-slate-100 hover:bg-slate-200 rounded transition">
                                        <i class="fa-solid fa-eye mr-1"></i> Lihat Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-check text-4xl mb-2 block"></i>
                                <p>Belum ada riwayat evaluasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
