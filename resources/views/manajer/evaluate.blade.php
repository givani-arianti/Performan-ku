@extends('layouts.app')

@section('title', 'Evaluasi Tugas')
@section('page_title', 'Evaluasi Tugas Karyawan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('manajer.employee.detail', $task->id_karyawan) }}" class="inline-flex items-center text-sm font-semibold text-navy-800 hover:text-navy-600">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Kembali ke Detail Kinerja Karyawan
        </a>
    </div>

    <!-- Task Information -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center gap-2">
            <i class="fa-solid fa-clipboard-list text-navy-800"></i> Informasi Tugas Karyawan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-slate-400 font-semibold mb-1">Nama Tugas</p>
                <p class="font-bold text-slate-800 text-base">{{ $task->judul_tugas }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold mb-1">Karyawan Pelaksana</p>
                <p class="font-semibold text-slate-800">{{ $task->karyawan->name ?? '-' }} ({{ $task->karyawan->email ?? '' }})</p>
            </div>
            <div class="col-span-1 md:col-span-2">
                <p class="text-xs text-slate-400 font-semibold mb-1">Target / Deskripsi Instruksi</p>
                <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100 whitespace-pre-line">{{ $task->deskripsi }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold mb-1">Batas Waktu (Deadline)</p>
                <p class="text-sm font-bold text-slate-800 flex items-center gap-1.5 mt-0.5">
                    <i class="fa-solid fa-calendar-day text-slate-400"></i>
                    {{ \Carbon\Carbon::parse($task->deadline)->format('d F Y') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold mb-1">Progres Pengerjaan Tugas</p>
                <div class="flex items-center space-x-3 mt-1">
                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-navy-800 h-2 rounded-full" style="width: {{ $task->persentase }}%"></div>
                    </div>
                    <span class="text-xs text-slate-800 font-extrabold">{{ $task->persentase }}%</span>
                </div>
            </div>
            @if($task->dokumen_path)
            <div class="col-span-1 md:col-span-2 pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-semibold mb-1.5">Bukti File Hasil Kerja</p>
                <a href="{{ Storage::url($task->dokumen_path) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold hover:bg-blue-100 border border-blue-200 transition">
                    <i class="fa-solid fa-file-arrow-down mr-2 text-sm"></i> Unduh / Buka File Hasil Kerja
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Automatic Performance Summary (Indikator Tugas & Bulanan) -->
    @php
        $kecepatan = $task->kecepatan_info;
        $monthlyKpi = $task->karyawan ? $task->karyawan->calculatePerformanceKpi() : null;
    @endphp

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-emerald-600"></i> Hasil Evaluasi Tugas & Kinerja Karyawan
        </h3>

        <!-- Task Speed & Timeliness cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl border bg-slate-50 border-slate-200">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Persentase Pengerjaan</span>
                <span class="text-2xl font-extrabold text-navy-800">{{ $task->persentase }}%</span>
                <p class="text-[10px] text-slate-400 mt-1">Penyelesaian target tugas ini</p>
            </div>

            <div class="p-4 rounded-xl border bg-slate-50 border-slate-200">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kecepatan Sesuai DL</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $kecepatan['color'] }} border mt-1">
                    <i class="fa-solid fa-gauge-high mr-1.5 text-[10px]"></i>
                    {{ $kecepatan['label'] }}
                </span>
                <p class="text-[10px] text-slate-400 mt-1">Kecepatan waktu penyelesaian</p>
            </div>

            @if($monthlyKpi)
            <div class="p-4 rounded-xl border bg-emerald-50/50 border-emerald-200">
                <span class="block text-xs font-semibold text-emerald-800 uppercase tracking-wider mb-1">Evaluasi Keseluruhan Bulan Ini</span>
                <span class="text-2xl font-extrabold text-emerald-700">{{ $monthlyKpi['final_score'] }}%</span>
                <p class="text-[10px] text-emerald-600 font-semibold mt-1">Predikat: {{ $monthlyKpi['category'] }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Evaluation Form / Details -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Form Catatan Review & Evaluasi Manajer</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pilih apakah hasil kerja sudah sesuai, atau membutuhkan perbaikan/revisi dari karyawan.</p>
            </div>
            @if($task->status === 'Reviewed')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    <i class="fa-solid fa-check mr-1 text-[10px]"></i> Sudah Dievaluasi
                </span>
            @elseif($task->status === 'Revisi')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                    <i class="fa-solid fa-arrows-rotate mr-1 text-[10px]"></i> Sedang Dalam Revisi
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    <i class="fa-solid fa-hourglass-half mr-1 text-[10px]"></i> Menunggu Evaluasi
                </span>
            @endif
        </div>
        
        <div class="p-6">
            <form action="{{ route('manajer.task.storeEvaluation', $task->id) }}" method="POST">
                @csrf
                
                <div class="space-y-5">
                    <div>
                        <label for="status_evaluasi" class="block text-xs font-bold text-slate-700 mb-1">
                            Status Hasil Evaluasi <span class="text-red-500">*</span>
                        </label>
                        <select id="status_evaluasi" name="status_evaluasi" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-navy-500 focus:border-navy-500 block p-3 font-semibold">
                            @php
                                $currentStatus = old('status_evaluasi', $task->evaluasi->status_evaluasi ?? '');
                            @endphp
                            <option value="" disabled {{ empty($currentStatus) ? 'selected' : '' }}>Pilih status evaluasi...</option>
                            <option value="Sesuai Ekspektasi" {{ $currentStatus === 'Sesuai Ekspektasi' ? 'selected' : '' }}>
                                ✅ Sesuai Ekspektasi (Tugas Diterima & Final)
                            </option>
                            <option value="Perlu Revisi" {{ $currentStatus === 'Perlu Revisi' ? 'selected' : '' }}>
                                ⚠️ Perlu Revisi (Kembalikan ke Karyawan untuk Diperbaiki)
                            </option>
                            <option value="Belum Sesuai Ekspektasi" {{ $currentStatus === 'Belum Sesuai Ekspektasi' ? 'selected' : '' }}>
                                ❌ Belum Sesuai Ekspektasi (Tugas Selesai dengan Nilai Evaluasi Kurang)
                            </option>
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1.5">
                            *Jika memilih <strong>Perlu Revisi</strong>, status tugas karyawan akan berubah menjadi <strong>Perlu Revisi</strong> dan karyawan dapat memperbaiki serta mengunggah ulang dokumen hasil kerja.
                        </p>
                        @error('status_evaluasi') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Lima Aspek Penilaian Kinerja -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4 space-y-3">
                        <div>
                            <h4 class="text-xs font-bold text-slate-700">Penilaian Aspek Kinerja (Opsional)</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Isi hanya bila ingin menilai manual (0–100). Biarkan kosong untuk memakai skor otomatis sistem ({{ $autoScore }}). Nilai akhir evaluasi adalah rata-rata kelima aspek.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach([
                                'kualitas' => 'Kualitas Hasil Kerja',
                                'komunikasi' => 'Komunikasi',
                                'kerja_sama' => 'Kerja Sama',
                                'disiplin_tanggung_jawab' => 'Disiplin & Tanggung Jawab',
                                'problem_solving' => 'Problem Solving',
                            ] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="block text-[11px] font-semibold text-slate-600 mb-1">
                                        {{ $label }} <span class="font-normal text-slate-400">(opsional)</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="{{ $field }}"
                                        name="{{ $field }}"
                                        min="0"
                                        max="100"
                                        placeholder="Otomatis: {{ $autoScore }}"
                                        value="{{ old($field, data_get($task->evaluasi, $field)) }}"
                                        class="w-full rounded-lg border-slate-300 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-navy-500 focus:ring-navy-500 py-2"
                                    >
                                    @error($field) <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="catatan" class="block text-xs font-bold text-slate-700 mb-1">
                            Catatan Evaluasi / Arahan Revisi Manajer <span class="text-red-500">*</span>
                        </label>
                        <textarea id="catatan" name="catatan" rows="4" required
                            class="bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-navy-500 focus:border-navy-500 block w-full p-3" 
                            placeholder="Tuliskan catatan apresiasi atau poin-poin yang harus diperbaiki oleh karyawan...">{{ old('catatan', $task->evaluasi->catatan ?? '') }}</textarea>
                        @error('catatan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- ======================================================= -->
                    <!-- SECTION: Tandai Poin Checklist yang Perlu Direvisi       -->
                    <!-- Hanya muncul jika status evaluasi = "Perlu Revisi"       -->
                    <!-- ======================================================= -->
                    <div id="section-checklist-revisi" class="hidden">
                        <div class="rounded-xl border-2 border-amber-300 bg-amber-50/40 overflow-hidden">
                            <div class="px-4 py-3 bg-amber-100/70 border-b border-amber-200 flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-amber-700"></i>
                                <h4 class="font-bold text-amber-900 text-sm">Tandai Poin Checklist yang Perlu Direvisi</h4>
                                <span class="ml-auto text-[10px] font-semibold text-amber-700 bg-amber-200 px-2 py-0.5 rounded-full">Opsional</span>
                            </div>

                            @if($task->checklists->count() > 0)
                                <div class="p-4 space-y-3">
                                    <p class="text-xs text-amber-800 font-medium mb-3">
                                        <i class="fa-solid fa-circle-info mr-1"></i>
                                        Centang poin yang harus karyawan perbaiki. Poin yang dicentang akan ditandai secara spesifik di halaman tugas karyawan.
                                        Poin yang dicentang juga akan otomatis di-<em>uncomplete</em> agar karyawan mengerjakan ulang.
                                    </p>

                                    @foreach($task->checklists as $item)
                                        <div class="rounded-lg border border-amber-200 bg-white p-3 space-y-2 transition">
                                            <!-- Checkbox: tandai poin ini perlu revisi -->
                                            <label class="flex items-start gap-2.5 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    name="checklist_ditandai[]"
                                                    value="{{ $item->id }}"
                                                    id="revisi_check_{{ $item->id }}"
                                                    {{ $item->perlu_revisi ? 'checked' : '' }}
                                                    onchange="toggleCatatanRevisi({{ $item->id }})"
                                                    class="mt-0.5 h-4 w-4 rounded border-amber-400 text-amber-600 focus:ring-amber-500 shrink-0"
                                                >
                                                <span class="text-xs font-semibold text-slate-800 leading-snug">
                                                    {{ $item->judul }}
                                                    @if($item->is_completed)
                                                        <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-700">
                                                            <i class="fa-solid fa-check mr-0.5"></i> Selesai
                                                        </span>
                                                    @endif
                                                </span>
                                            </label>

                                            <!-- Textarea catatan per poin (muncul saat checkbox dicentang) -->
                                            <div id="catatan_revisi_wrapper_{{ $item->id }}"
                                                class="{{ $item->perlu_revisi ? '' : 'hidden' }} pl-6">
                                                <input
                                                    type="text"
                                                    name="checklist_revisi[{{ $item->id }}]"
                                                    id="catatan_revisi_{{ $item->id }}"
                                                    value="{{ old('checklist_revisi.'.$item->id, $item->catatan_revisi ?? '') }}"
                                                    placeholder="Catatan khusus untuk poin ini (opsional)…"
                                                    class="w-full rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs text-slate-700 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-400 placeholder-slate-400"
                                                >
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 text-center text-xs text-amber-700">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    Tugas ini tidak memiliki checklist poin. Gunakan kolom catatan di atas untuk arahan revisi.
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- ======================================================= -->
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('manajer.tasks') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-navy-800 hover:bg-navy-700 text-white font-bold py-2.5 px-6 rounded-xl shadow transition flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Hasil Evaluasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle tampilan section checklist revisi berdasarkan pilihan status evaluasi
    const statusSelect    = document.getElementById('status_evaluasi');
    const sectionChecklist = document.getElementById('section-checklist-revisi');

    function updateChecklistSection() {
        if (statusSelect.value === 'Perlu Revisi') {
            sectionChecklist.classList.remove('hidden');
        } else {
            sectionChecklist.classList.add('hidden');
        }
    }

    // Jalankan saat halaman load (jika sudah ada nilai dari old() atau existing)
    document.addEventListener('DOMContentLoaded', function () {
        updateChecklistSection();
        statusSelect.addEventListener('change', updateChecklistSection);
    });

    // Toggle input catatan per poin saat checkbox dicentang/dilepas
    function toggleCatatanRevisi(checklistId) {
        const checkbox = document.getElementById('revisi_check_' + checklistId);
        const wrapper  = document.getElementById('catatan_revisi_wrapper_' + checklistId);
        if (checkbox.checked) {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
            // Kosongkan input catatan saat dilepas
            const input = document.getElementById('catatan_revisi_' + checklistId);
            if (input) input.value = '';
        }
    }
</script>
@endsection
