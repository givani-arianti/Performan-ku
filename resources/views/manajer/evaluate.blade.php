@extends('layouts.app')

@section('title', 'Evaluasi Tugas')
@section('page_title', 'Evaluasi Tugas Karyawan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('manajer.employee.detail', $task->id_karyawan) }}" class="inline-flex items-center text-sm font-semibold text-navy-800 hover:text-navy-600">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Kembali ke Detail Karyawan
        </a>
    </div>

    <!-- Task Information -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Informasi Tugas</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-slate-400 font-semibold mb-1">Nama Tugas</p>
                <p class="font-semibold text-slate-800">{{ $task->judul_tugas }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold mb-1">Karyawan</p>
                <p class="font-semibold text-slate-800">{{ $task->karyawan->name ?? '-' }}</p>
            </div>
            <div class="col-span-1 md:col-span-2">
                <p class="text-xs text-slate-400 font-semibold mb-1">Target / Deskripsi</p>
                <p class="text-sm text-slate-600">{{ $task->target }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold mb-1">Batas Waktu</p>
                <p class="text-sm text-slate-600">{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold mb-1">Status Progres</p>
                <div class="flex items-center space-x-3 mt-1">
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-navy-800 h-1.5 rounded-full" style="width: {{ $task->persentase }}%"></div>
                    </div>
                    <span class="text-xs text-slate-600 font-bold">{{ $task->persentase }}%</span>
                </div>
            </div>
            @if($task->dokumen_path)
            <div class="col-span-1 md:col-span-2">
                <p class="text-xs text-slate-400 font-semibold mb-1">Bukti Dokumen</p>
                <a href="{{ Storage::url($task->dokumen_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-slate-100 text-navy-800 rounded text-sm hover:bg-slate-200 transition">
                    <i class="fa-solid fa-file-arrow-down mr-2"></i> Lihat/Unduh Bukti
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Evaluation Form / Details -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-800">Penilaian Kinerja</h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('manajer.task.storeEvaluation', $task->id) }}" method="POST">
                @csrf
                
                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-semibold text-slate-700 mb-1">Komponen Penilaian (0-100)</p>
                        <p class="text-xs text-slate-500 mb-3">Performance KPI (Bobot 60%): Target, Ketepatan Waktu, Kualitas. Soft Skill (Bobot 30%): Komunikasi (25%), Kerja sama (25%), Disiplin (25%), Problem Solving (25%). Absensi (Bobot 10%).</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach([
                                'kualitas' => 'Kualitas Hasil Kerja',
                                'komunikasi' => 'Komunikasi',
                                'kerja_sama' => 'Kerja Sama Tim',
                                'disiplin_tanggung_jawab' => 'Disiplin dan Tanggung Jawab',
                                'problem_solving' => 'Problem Solving',
                            ] as $field => $label)
                                <div>
                                    <label for="{{ $field }}" class="block text-xs font-semibold text-slate-600 mb-1">{{ $label }}</label>
                                    @if($task->status === 'Reviewed')
                                        <div class="bg-emerald-50 text-emerald-700 font-bold rounded-lg border border-emerald-200 p-2.5">{{ $task->evaluasi->{$field} ?? $task->evaluasi->score }}</div>
                                    @else
                                        @if($field === 'kualitas')
                                            <select id="{{ $field }}" name="{{ $field }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-navy-500 focus:border-navy-500 block p-2.5 font-bold">
                                                <option value="">Pilih nilai kualitas</option>
                                                @foreach([100 => '100 - Sangat Baik', 80 => '80 - Baik', 60 => '60 - Cukup', 40 => '40 - Kurang'] as $value => $qualityLabel)
                                                    <option value="{{ $value }}" {{ old($field) == $value ? 'selected' : '' }}>{{ $qualityLabel }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="number" id="{{ $field }}" name="{{ $field }}" min="0" max="100" required value="{{ old($field) }}"
                                                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-navy-500 focus:border-navy-500 block p-2.5 font-bold" placeholder="0-100">
                                        @endif
                                    @endif
                                    @error($field) <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="status_evaluasi" class="block text-sm font-semibold text-slate-700 mb-1">Status Evaluasi</label>
                        @if($task->status === 'Reviewed')
                            <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700">
                                {{ $task->evaluasi->status_evaluasi }}
                            </div>
                        @else
                            <select id="status_evaluasi" name="status_evaluasi" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-navy-500 focus:border-navy-500 block p-2.5">
                                <option value="">Pilih status evaluasi</option>
                                <option value="Sesuai Ekspektasi" {{ old('status_evaluasi') === 'Sesuai Ekspektasi' ? 'selected' : '' }}>Sesuai Ekspektasi</option>
                                <option value="Belum Sesuai Ekspektasi" {{ old('status_evaluasi') === 'Belum Sesuai Ekspektasi' ? 'selected' : '' }}>Belum Sesuai Ekspektasi</option>
                            </select>
                        @endif
                        @error('status_evaluasi') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="catatan" class="block text-sm font-semibold text-slate-700 mb-1">Catatan Evaluasi</label>
                        @if($task->status === 'Reviewed')
                            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 text-sm text-slate-700">
                                {{ $task->evaluasi->catatan ?: 'Tidak ada catatan.' }}
                            </div>
                        @else
                            <textarea id="catatan" name="catatan" rows="4" required
                                class="bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-navy-500 focus:border-navy-500 block w-full p-2.5" 
                                placeholder="Berikan feedback atau catatan perbaikan untuk karyawan..."></textarea>
                        @endif
                        @error('catatan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if($task->status !== 'Reviewed')
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition">
                        Simpan Evaluasi
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
