@extends('layouts.app')

@section('title', 'Detail Tugas')
@section('page_title', 'Detail & Progres Tugas')

@section('content')
<div class="space-y-6">
    <!-- Back to tasks -->
    <div>
        <a href="{{ route('karyawan.tasks') }}" class="inline-flex items-center text-sm font-semibold text-navy-800 hover:text-navy-600">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Kembali ke Daftar Tugas
        </a>
    </div>

    <!-- Task details grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Task Info (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
                <!-- Header -->
                <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ $task->judul_tugas }}</h2>
                        <p class="text-xs text-slate-400 mt-1">Ditugaskan oleh HR pada {{ $task->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        @if($task->status === 'Pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                Pending
                            </span>
                        @elseif($task->status === 'In Progress')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                Dalam Proses
                            </span>
                        @elseif($task->status === 'Completed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                Menunggu Evaluasi
                            </span>
                        @elseif($task->status === 'Reviewed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                Sudah Dievaluasi
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Tugas</h4>
                    <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100 whitespace-pre-line">
                        {{ $task->deskripsi }}
                    </p>
                </div>

                <!-- Targets & Deadlines -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Target Pekerjaan</span>
                        <span class="text-sm font-bold text-slate-800">{{ $task->target }}</span>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Batas Waktu (Deadline)</span>
                        <span class="text-sm font-bold text-slate-800 flex items-center">
                            <i class="fa-solid fa-calendar-days text-slate-500 mr-2"></i>
                            {{ \Carbon\Carbon::parse($task->deadline)->format('d F Y') }}
                        </span>
                    </div>
                </div>

                <!-- Final Work Submission comment if completed -->
                @if($task->hasil_kerja || $task->dokumen_path)
                    <div class="border-t border-slate-100 pt-6">
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">File Hasil Kerja</h4>
                        @if($task->dokumen_path)
                            <a href="{{ Storage::url($task->dokumen_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 text-sm font-semibold hover:bg-blue-100 transition">
                                <i class="fa-solid fa-file-arrow-down mr-2"></i>
                                {{ $task->hasil_kerja ?: 'Buka file hasil kerja' }}
                            </a>
                        @else
                            <p class="text-sm text-slate-700">{{ $task->hasil_kerja }}</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Manager Evaluation Result (If Reviewed) -->
            @if($task->evaluasi)
                <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-emerald-100 text-emerald-800 rounded-lg">
                                <i class="fa-solid fa-file-signature text-lg"></i>
                            </div>
                            <h3 class="font-bold text-slate-800">Hasil Evaluasi Manajer</h3>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400">Tanggal: {{ \Carbon\Carbon::parse($task->evaluasi->tanggal_evaluasi)->format('d M Y') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-lg flex flex-col justify-center">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Performa</span>
                            @if($task->evaluasi->status_evaluasi === 'Sesuai Ekspektasi')
                                <span class="text-sm font-bold text-emerald-600 flex items-center">
                                    <i class="fa-solid fa-circle-check mr-1.5"></i> Sesuai Ekspektasi
                                </span>
                            @else
                                <span class="text-sm font-bold text-red-600 flex items-center">
                                    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Belum Sesuai Ekspektasi
                                </span>
                            @endif
                        </div>
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-lg flex flex-col justify-center">
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nilai Evaluasi</span>
                            <span class="text-xl font-extrabold text-navy-800">{{ $task->evaluasi->score }} / 100</span>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Catatan / Rekomendasi Manajer</h4>
                        <p class="text-sm text-slate-700 bg-slate-50 border border-slate-100 p-4 rounded-lg leading-relaxed whitespace-pre-line">
                            {{ $task->evaluasi->catatan }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Progress Update Log / History -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-6 border-b border-slate-100 pb-4">Histori Progres Tugas</h3>
                <div class="relative pl-6 border-l-2 border-slate-100 space-y-6">
                    @forelse($task->progress as $log)
                        <div class="relative">
                            <!-- Bullet -->
                            <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-navy-600 flex items-center justify-center text-[7px] text-white"></div>
                            
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-slate-800">Progres: {{ $log->persentase }}%</span>
                                    <span class="text-xs text-slate-400 font-medium">
                                        {{ \Carbon\Carbon::parse($log->tanggal_update)->format('d M Y - H:i') }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 bg-slate-50 p-2.5 rounded-lg border border-slate-100 whitespace-pre-line">
                                    {{ $log->keterangan }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 py-6">
                            <i class="fa-solid fa-timeline text-2xl mb-1.5"></i>
                            <p class="text-xs">Belum ada progres yang diupdate.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Actions Panel (1 col) -->
        <div class="space-y-6">
            @if(in_array($task->status, ['Pending', 'In Progress']))
            <!-- 1. Checklist Subtugas Box -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-800 flex items-center text-sm">
                        <i class="fa-solid fa-list-check mr-2 text-navy-800"></i>
                        Checklist Subtugas
                    </h3>
                    <span class="text-xs font-bold text-navy-800 bg-slate-100 px-2 py-0.5 rounded-full">
                        {{ $task->persentase }}% Selesai
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-navy-800 h-2 rounded-full transition-all duration-300" style="width: {{ $task->persentase }}%"></div>
                </div>

                @if($task->checklists->count() > 0)
                    <div class="space-y-2">
                        @foreach($task->checklists as $item)
                            <form action="{{ route('karyawan.task.checklist.toggle', ['taskId' => $task->id, 'checklistId' => $item->id]) }}" method="POST">
                                @csrf
                                <label class="flex items-start p-2.5 rounded-lg border {{ $item->is_completed ? 'bg-emerald-50/60 border-emerald-200 text-slate-700' : 'bg-slate-50 border-slate-200 text-slate-800 hover:bg-slate-100' }} transition cursor-pointer">
                                    <input type="checkbox" onchange="this.form.submit()" {{ $item->is_completed ? 'checked' : '' }} {{ !in_array($task->status, ['Pending', 'In Progress']) ? 'disabled' : '' }} class="mt-0.5 h-4 w-4 rounded border-slate-300 text-navy-800 focus:ring-navy-800">
                                    <span class="ml-2.5 text-xs font-medium {{ $item->is_completed ? 'line-through text-slate-500' : 'text-slate-800' }}">
                                        {{ $item->judul }}
                                    </span>
                                </label>
                            </form>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic py-2 text-center">Manajer belum membuat daftar checklist subtugas untuk tugas ini.</p>
                @endif
            </div>

                <!-- 2. Final Submission Form -->
                <div class="bg-white rounded-xl border border-blue-200 shadow-sm p-5">
                    <h3 class="font-bold text-slate-800 mb-1 flex items-center">
                        <i class="fa-solid fa-circle-check mr-2 text-blue-600"></i>
                        Selesaikan Tugas
                    </h3>
                    <p class="text-[10px] text-slate-400 mb-4 border-b border-slate-100 pb-3">Unggah PDF atau gambar jika semua target sudah tercapai 100%.</p>
                    <form action="{{ route('karyawan.task.submit', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="return confirm('Apakah Anda yakin target tugas sudah selesai 100% dan siap dikirim ke manajer?')">
                        @csrf
                        <div>
                            <label for="dokumen" class="block text-xs font-semibold text-slate-600 mb-1">File Hasil Kerja</label>
                            <input type="file" id="dokumen" name="dokumen" accept=".pdf,.jpg,.jpeg,.png,.webp" required
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-xs bg-white">
                            <p class="text-[10px] text-slate-400 mt-1">Format: PDF, JPG, JPEG, PNG, atau WEBP. Maksimal 10 MB.</p>
                            @error('dokumen') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white font-semibold text-xs py-2.5 rounded-lg hover:bg-blue-700 shadow transition flex items-center justify-center">
                            <i class="fa-solid fa-paper-plane mr-1.5"></i> Selesaikan & Kirim
                        </button>
                    </form>
                </div>
            @else
                <!-- Task is completed and awaiting review / reviewed -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 text-center">
                    <div class="text-4xl text-slate-300 mb-3">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">Tugas Terkunci</h3>
                    <p class="text-xs text-slate-400 mt-1">Tugas ini sudah diselesaikan dan tidak dapat diedit lagi.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
