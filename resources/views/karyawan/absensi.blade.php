@extends('layouts.app')

@section('title', 'Absensi Karyawan')
@section('page_title', 'Absensi Karyawan')

@section('content')
<div class="space-y-6">
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Absensi Hari Ini</h3>
                <p class="text-sm text-slate-500 mt-1">Catat kehadiran kerja Anda dengan cepat dan akurat.</p>
            </div>
            <div class="inline-flex items-center px-3 py-2 rounded-lg bg-navy-50 border border-navy-100 text-navy-800 text-sm font-semibold">
                <i class="fa-solid fa-calendar-day mr-2"></i>
                {{ now()->translatedFormat('d F Y') }} 
                <span class="mx-2">|</span>
                <i class="fa-solid fa-clock mr-2"></i>
                <span id="realtime-clock" class="font-mono">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>

<script>
    // Update jam secara realtime menggunakan waktu lokal perangkat (laptop/HP)
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('realtime-clock').textContent = hours + ':' + minutes + ':' + seconds;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Status</p>
                <div class="mt-3 inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold border {{ $status['color'] }}">
                    {{ $status['label'] }}
                </div>
                <p class="mt-2 text-sm text-slate-600">{{ $status['detail'] }}</p>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Jam</p>
                <p class="mt-2 text-lg font-bold text-slate-800">
                    {{ $checkIn ? substr($checkIn, 0, 5) : '--:--' }}
                    <span class="text-sm font-medium text-slate-500"> / </span>
                    {{ $checkOut ? substr($checkOut, 0, 5) : '--:--' }}
                </p>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <form action="{{ route('karyawan.absensi.store') }}" method="POST" class="flex-1" id="form-masuk">
                @csrf
                <input type="hidden" name="jenis" value="masuk">
                <input type="hidden" name="client_time" id="client_time_masuk">
                <button type="submit" onclick="setClientTime('client_time_masuk')" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl bg-navy-800 text-white font-semibold hover:bg-navy-700 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed" {{ $checkIn ? 'disabled' : '' }}>
                    <i class="fa-solid fa-right-to-bracket mr-2"></i>
                    Absen Masuk
                </button>
            </form>

            <form action="{{ route('karyawan.absensi.store') }}" method="POST" class="flex-1" id="form-pulang">
                @csrf
                <input type="hidden" name="jenis" value="pulang">
                <input type="hidden" name="client_time" id="client_time_pulang">
                <button type="submit" onclick="setClientTime('client_time_pulang')" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl bg-slate-800 text-white font-semibold hover:bg-slate-700 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed" {{ $checkOut || !$checkIn ? 'disabled' : '' }}>
                    <i class="fa-solid fa-right-from-bracket mr-2"></i>
                    Absen Pulang
                </button>
            </form>

            <button type="button" onclick="toggleIzinModal()" class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-xl bg-[#4d7a6b] text-white font-semibold hover:bg-[#3d6356] transition duration-150 shadow-sm">
                <i class="fa-solid fa-file-medical mr-2"></i>
                Ajukan Cuti / Izin / Sakit
            </button>
        </div>
    </div>

    <!-- MODAL PENGAJUAN IZIN/CUTI -->
    <div id="modal-izin" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-[#21352f] text-white flex items-center justify-between">
                <h3 class="font-bold text-base flex items-center gap-2">
                    <i class="fa-solid fa-file-medical text-[#4d7a6b]"></i> Form Pengajuan Cuti / Izin / Sakit
                </h3>
                <button type="button" onclick="toggleIzinModal()" class="text-white hover:text-red-200">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('karyawan.absensi.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Pengajuan</label>
                    <select name="jenis" required class="w-full rounded-xl border-slate-300 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2.5">
                        <option value="cuti">Cuti Tahunan / Alasan Penting</option>
                        <option value="sakit">Sakit (Surat Dokter)</option>
                        <option value="izin">Izin Kepentingan Pribadi</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] py-2.5">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pengajuan</label>
                    <textarea name="alasan" rows="3" required placeholder="Jelaskan alasan pengajuan cuti/izin/sakit secara singkat..." class="w-full rounded-xl border-slate-300 text-sm focus:border-[#4d7a6b] focus:ring-[#4d7a6b] p-3"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Dokumen Bukti (Opsional)</label>
                    <input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#f4f2ee] file:text-[#21352f] hover:file:bg-[#e4e2de]">
                    <p class="text-[10px] text-slate-400 mt-1">Format PDF, JPG, PNG (Maks 2MB). Wajib untuk pengajuan Sakit.</p>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="toggleIzinModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#21352f] text-white text-xs font-bold hover:bg-[#1a2a24] transition shadow">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
    function setClientTime(inputId) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById(inputId).value = hours + ':' + minutes + ':' + seconds;
    }

    function toggleIzinModal() {
        const modal = document.getElementById('modal-izin');
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }
</script>

    <!-- TABLE STATUS PENGAJUAN CUTI/IZIN/SAKIT -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-notes-medical text-[#4d7a6b]"></i> Status Pengajuan Cuti / Izin / Sakit Anda
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Rentang Tanggal</th>
                        <th class="px-6 py-3">Alasan</th>
                        <th class="px-6 py-3">Dokumen</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($pengajuanIzinList as $izin)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-bold uppercase text-slate-700">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $izin->jenis === 'sakit' ? 'bg-red-50 text-red-700' : ($izin->jenis === 'cuti' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700') }}">
                                    {{ $izin->jenis }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-slate-700 font-medium">
                                {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-3 text-slate-600 max-w-xs truncate">
                                {{ $izin->alasan }}
                            </td>
                            <td class="px-6 py-3">
                                @if($izin->dokumen)
                                    <a href="{{ asset('storage/' . $izin->dokumen) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-paperclip"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @if($izin->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check mr-1"></i> Disetujui
                                    </span>
                                @elseif($izin->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800" title="{{ $izin->catatan_approver }}">
                                        <i class="fa-solid fa-xmark mr-1"></i> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-clock mr-1"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-slate-400">
                                Belum ada riwayat pengajuan cuti/izin/sakit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="font-bold text-slate-800">Riwayat Absensi</h3>
            <form action="{{ route('karyawan.absensi') }}" method="GET" class="flex items-center gap-2">
                <select name="bulan" class="rounded-lg border-slate-300 text-sm focus:border-navy-500 focus:ring-navy-500 py-2">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <select name="tahun" class="rounded-lg border-slate-300 text-sm focus:border-navy-500 focus:ring-navy-500 py-2">
                    @foreach(range(now()->year - 2, now()->year) as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-2 bg-navy-50 text-navy-700 hover:bg-navy-100 rounded-lg text-sm font-semibold transition border border-navy-100">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Jenis</th>
                        <th class="px-6 py-3.5">Jam</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentAbsensi as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-700">{{ $item['tanggal'] }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $item['jenis'] === 'masuk' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $item['jenis'] === 'masuk' ? 'Masuk' : 'Pulang' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ substr($item['jam'], 0, 5) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $item['status'] === 'Terlambat' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                    {{ $item['status'] ?? 'Tepat waktu' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-slate-400">
                                <i class="fa-solid fa-clock text-2xl mb-2 block"></i>
                                Belum ada riwayat absensi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
