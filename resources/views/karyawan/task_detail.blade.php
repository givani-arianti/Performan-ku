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

            <!-- Alert Notice if Task needs Revision -->
            @if($task->status === 'Revisi')
                @php
                    $poinRevisi = $task->checklists->where('perlu_revisi', true);
                    $totalPoin  = $task->checklists->count();
                @endphp
                <div class="p-5 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-900 shadow-sm flex flex-col sm:flex-row items-start gap-4">
                    <div class="h-11 w-11 rounded-xl bg-amber-200 text-amber-800 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                            <h3 class="font-extrabold text-base text-amber-900">Perhatian: Tugas Memerlukan Revisi dari Manajer</h3>
                            @if($task->evaluasi)
                                <span class="text-[11px] font-bold text-amber-800 bg-amber-200/80 px-2.5 py-0.5 rounded-full self-start">
                                    {{ \Carbon\Carbon::parse($task->evaluasi->tanggal_evaluasi)->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-amber-800 font-medium">
                            Manajer telah meninjau pekerjaan Anda dan meminta beberapa perbaikan berikut:
                        </p>
                        <div class="mt-2.5 bg-white p-4 rounded-xl border border-amber-200 text-xs text-slate-800 font-semibold leading-relaxed shadow-inner whitespace-pre-line">
                            {{ $task->evaluasi->catatan ?? 'Silakan sesuaikan kembali hasil kerja dengan instruksi manajer.' }}
                        </div>

                        {{-- Summary poin checklist yang perlu direvisi --}}
                        @if($totalPoin > 0 && $poinRevisi->count() > 0)
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-100 text-red-800 border border-red-300 text-[11px] font-bold">
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                    {{ $poinRevisi->count() }} dari {{ $totalPoin }} poin checklist perlu diperbaiki
                                </span>
                                <span class="text-[11px] text-amber-700">— lihat highlight merah di checklist sebelah kanan.</span>
                            </div>
                        @endif

                        <div class="mt-3 flex items-center text-xs text-amber-800 font-medium">
                            <i class="fa-solid fa-circle-info mr-1.5 text-amber-600"></i>
                            Silakan lakukan perbaikan tugas, sesuaikan checklist (jika ada), lalu unggah file hasil revisi pada formulir di sebelah kanan.
                        </div>
                    </div>
                </div>
            @endif

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
                        @elseif($task->status === 'Revisi')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                <i class="fa-solid fa-arrows-rotate mr-1 text-[10px]"></i> Perlu Revisi
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

                <!-- Targets & Deadlines & Speed -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Progres Pengerjaan</span>
                        <span class="text-xl font-extrabold text-navy-800">{{ $task->persentase }}%</span>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Batas Waktu (Deadline)</span>
                        <span class="text-sm font-bold text-slate-800 flex items-center mt-1">
                            <i class="fa-solid fa-calendar-days text-slate-500 mr-2"></i>
                            {{ \Carbon\Carbon::parse($task->deadline)->format('d F Y') }}
                        </span>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-lg">
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kecepatan Sesuai DL</span>
                        @php $kecepatan = $task->kecepatan_info; @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $kecepatan['color'] }} border mt-0.5">
                            <i class="fa-solid fa-gauge-high mr-1.5 text-[10px]"></i>
                            {{ $kecepatan['label'] }}
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

            <!-- Progress Update Log / History -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Histori Progres Tugas</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Jejak aktivitas pengerjaan hingga evaluasi manajer</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">
                        <i class="fa-solid fa-list-ul text-[9px]"></i>
                        {{ $task->progress->count() }} Aktivitas
                    </span>
                </div>

                <div class="p-6">
                    @if($task->progress->isEmpty())
                        <div class="text-center text-slate-400 py-10">
                            <div class="mx-auto h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mb-2">
                                <i class="fa-solid fa-timeline text-xl"></i>
                            </div>
                            <p class="text-xs font-medium">Belum ada progres yang diupdate.</p>
                        </div>
                    @else
                        <div class="relative">
                            <div class="absolute left-4 top-3 bottom-3 w-px bg-slate-200"></div>

                            <div class="space-y-4">
                                @foreach($task->progress as $log)
                                    @php
                                        $keterangan = (string) $log->keterangan;
                                        $isEvaluasi = str_contains($keterangan, 'Evaluasi selesai');
                                        $isPermintaanRevisi = str_contains($keterangan, 'perbaikan/revisi');
                                        $isPengiriman = str_contains($keterangan, 'Tugas selesai') || str_contains($keterangan, 'diunggah');

                                        $ikon = $isEvaluasi
                                            ? 'fa-file-signature'
                                            : ($isPermintaanRevisi
                                                ? 'fa-triangle-exclamation'
                                                : ($isPengiriman ? 'fa-cloud-arrow-up' : 'fa-arrow-trend-up'));

                                        $warna = $isEvaluasi
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : ($isPermintaanRevisi
                                                ? 'bg-amber-100 text-amber-700'
                                                : ($isPengiriman ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600'));
                                    @endphp

                                    <div class="relative flex gap-4">
                                        <div class="relative z-10 h-8 w-8 shrink-0 rounded-full border-2 border-white shadow-sm flex items-center justify-center {{ $warna }}">
                                            <i class="fa-solid {{ $ikon }} text-[11px]"></i>
                                        </div>

                                        <div class="flex-1 rounded-xl border bg-white p-4 {{ $loop->first ? 'border-slate-200 shadow-sm' : 'border-slate-100' }}">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-800">Progres {{ $log->persentase }}%</span>
                                                    @if($loop->first)
                                                        <span class="px-1.5 py-0.5 rounded-md bg-navy-50 text-navy-800 border border-navy-100 text-[9px] font-bold uppercase tracking-wide">Terbaru</span>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-slate-400 font-medium">
                                                    <i class="fa-regular fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($log->tanggal_update)->format('d M Y, H:i') }} WIB
                                                </span>
                                            </div>
                                            <p class="text-xs text-slate-600 mt-2 leading-relaxed whitespace-pre-line">{{ $log->keterangan }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Actions Panel (1 col) -->
        <div class="space-y-6">
            @if(in_array($task->status, ['Pending', 'In Progress', 'Revisi']))
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
                                <div class="rounded-lg overflow-hidden border {{ $item->perlu_revisi ? 'border-red-300 bg-red-50/40' : 'border-transparent' }}">
                                    <form action="{{ route('karyawan.task.checklist.toggle', ['taskId' => $task->id, 'checklistId' => $item->id]) }}" method="POST">
                                        @csrf
                                        <label class="flex items-start p-2.5 rounded-lg {{ $item->perlu_revisi ? '' : 'border' }} {{ $item->is_completed ? 'bg-emerald-50/60 border-emerald-200 text-slate-700' : 'bg-slate-50 border-slate-200 text-slate-800 hover:bg-slate-100' }} transition cursor-pointer">
                                            <input type="checkbox" onchange="this.form.submit()" {{ $item->is_completed ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-slate-300 text-navy-800 focus:ring-navy-800">
                                            <span class="ml-2.5 text-xs font-medium {{ $item->is_completed ? 'line-through text-slate-500' : 'text-slate-800' }}">
                                                {{ $item->judul }}
                                            </span>
                                            @if($item->perlu_revisi)
                                                <span class="ml-auto shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 border border-red-300">
                                                    <i class="fa-solid fa-circle-exclamation text-[8px]"></i> Perlu Revisi
                                                </span>
                                            @endif
                                        </label>
                                    </form>

                                    {{-- Catatan revisi dari manajer untuk poin ini --}}
                                    @if($item->perlu_revisi && $item->catatan_revisi)
                                        <div class="mx-2.5 mb-2.5 px-3 py-2 rounded-lg bg-red-100 border border-red-200 flex items-start gap-2">
                                            <i class="fa-solid fa-comment-dots text-red-500 mt-0.5 text-xs shrink-0"></i>
                                            <p class="text-[11px] text-red-800 font-semibold leading-snug">{{ $item->catatan_revisi }}</p>
                                        </div>
                                    @elseif(!$item->perlu_revisi && $item->catatan_revisi && $task->status === 'Reviewed')
                                        {{-- Histori catatan revisi yang sudah diselesaikan --}}
                                        <div class="mx-2.5 mb-2.5 px-3 py-2 rounded-lg bg-slate-100 border border-slate-200 flex items-start gap-2">
                                            <i class="fa-solid fa-clock-rotate-left text-slate-400 mt-0.5 text-xs shrink-0"></i>
                                            <p class="text-[11px] text-slate-500 leading-snug"><span class="font-semibold text-slate-600">Catatan revisi sebelumnya:</span> {{ $item->catatan_revisi }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic py-2 text-center">Manajer belum membuat daftar checklist subtugas untuk tugas ini.</p>
                    @endif
                </div>

                @if($task->status === 'Revisi')
                    <!-- 2. Revision Submission Form -->
                    <div class="bg-white rounded-xl border-2 border-amber-300 shadow-sm p-5 space-y-3">
                        <div class="flex items-center space-x-2 text-amber-900 font-bold">
                            <i class="fa-solid fa-cloud-arrow-up text-amber-600"></i>
                            <h3 class="text-sm">Kirim Dokumen Hasil Revisi</h3>
                        </div>
                        <p class="text-[11px] text-slate-500 border-b border-slate-100 pb-3">
                            Unggah dokumen/bukti kerja yang sudah Anda perbaiki sesuai arahan revisi manajer.
                        </p>
                        <form action="{{ route('karyawan.task.submit', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="return confirm('Apakah Anda yakin dokumen revisi sudah sesuai instruksi dan siap dikirimkan kembali ke manajer?')">
                            @csrf
                            <div>
                                <label for="dokumen_revisi" class="block text-xs font-semibold text-slate-700 mb-1">Pilih File Hasil Revisi</label>
                                <input type="file" id="dokumen_revisi" name="dokumen" accept=".pdf,.jpg,.jpeg,.png,.webp" required
                                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 text-xs bg-slate-50">
                                <p class="text-[10px] text-slate-400 mt-1">Format: PDF, JPG, JPEG, PNG, WEBP. Maks 10 MB.</p>
                                @error('dokumen') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs py-2.5 rounded-lg shadow transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Ulang Hasil Revisi
                            </button>
                        </form>
                    </div>
                @else
                    <!-- 2. Normal Final Submission Form -->
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
                @endif
            @else
                <!-- Task is Completed or Reviewed -->
                @if($task->status === 'Completed')
                    <div class="bg-white rounded-xl border border-blue-200 shadow-sm p-6 text-center space-y-3">
                        <div class="w-14 h-14 mx-auto rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-hourglass-half animate-pulse"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Menunggu Evaluasi Manajer</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Dokumen hasil kerja Anda telah berhasil dikirim. Saat ini manajer sedang meninjau dan menilai pekerjaan Anda.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 bg-gradient-to-r from-emerald-700 to-emerald-600 text-white">
                            <div class="flex items-center gap-2.5">
                                <div class="h-9 w-9 rounded-xl bg-white/15 flex items-center justify-center">
                                    <i class="fa-solid fa-award"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm">Tugas Selesai &amp; Dievaluasi</h3>
                                    <p class="text-[10px] text-emerald-50">Penilaian manajer sudah final</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-4">
                            @if($task->evaluasi)
                                @php $nilaiAkhir = max(0, min(100, (int) $task->evaluasi->score)); @endphp

                                <div class="flex items-end justify-between gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai Akhir</p>
                                        <p class="text-3xl font-black text-slate-800 leading-none mt-1">
                                            {{ $nilaiAkhir }}<span class="text-sm font-semibold text-slate-400">/100</span>
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $task->evaluasi->status_evaluasi === 'Sesuai Ekspektasi' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($task->evaluasi->status_evaluasi === 'Perlu Revisi' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-red-50 text-red-700 border-red-200') }}">
                                        <i class="fa-solid {{ $task->evaluasi->status_evaluasi === 'Sesuai Ekspektasi' ? 'fa-circle-check' : 'fa-triangle-exclamation' }} text-[9px]"></i>
                                        {{ $task->evaluasi->status_evaluasi }}
                                    </span>
                                </div>

                                <div class="rounded-xl border border-slate-100 bg-slate-50 divide-y divide-slate-100 text-[11px]">
                                    <div class="flex items-center justify-between px-3 py-2.5">
                                        <span class="text-slate-500">Manajer</span>
                                        <span class="font-semibold text-slate-800">{{ $task->evaluasi->manajer?->name ?? 'Manajer' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between px-3 py-2.5">
                                        <span class="text-slate-500">Tanggal Evaluasi</span>
                                        <span class="font-semibold text-slate-800">{{ $task->evaluasi->tanggal_evaluasi ? \Carbon\Carbon::parse($task->evaluasi->tanggal_evaluasi)->format('d M Y') : '-' }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-slate-500">Evaluasi untuk tugas ini telah selesai dilakukan oleh manajer.</p>
                            @endif

                            @if($task->dokumen_path)
                                <a href="{{ Storage::url($task->dokumen_path) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                                    <i class="fa-solid fa-paperclip"></i> Buka File Hasil Kerja
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
