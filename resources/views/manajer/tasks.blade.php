@extends('layouts.app')

@section('title', 'Kelola Tugas')
@section('page_title', 'Kelola Tugas Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Top Action bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">Delegasikan tugas kerja baru beserta target dan batas waktu penyelesaian kepada karyawan.</p>
        </div>
        <button onclick="toggleModal('create-modal')" class="bg-navy-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg hover:bg-navy-700 shadow transition flex items-center self-start">
            <i class="fa-solid fa-plus mr-1.5"></i> Buat Tugas Baru
        </button>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('manajer.tasks') }}" class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Karyawan</label>
            <select name="karyawan_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-navy-500 focus:ring-navy-500 py-2">
                <option value="">Semua Karyawan</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('karyawan_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 w-full">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Divisi</label>
            <select name="divisi" class="w-full rounded-lg border-slate-300 text-sm focus:border-navy-500 focus:ring-navy-500 py-2">
                <option value="">Semua Divisi</option>
                @foreach($divisiList as $divisi)
                    <option value="{{ $divisi }}" {{ request('divisi') == $divisi ? 'selected' : '' }}>{{ $divisi }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button type="submit" class="w-full sm:w-auto bg-navy-50 text-navy-700 hover:bg-navy-100 font-semibold px-4 py-2 rounded-lg text-sm border border-navy-100 transition">Filter</button>
            @if(request()->filled('karyawan_id') || request()->filled('divisi'))
                <a href="{{ route('manajer.tasks') }}" class="w-full sm:w-auto text-center bg-slate-50 text-slate-600 hover:bg-slate-100 font-semibold px-4 py-2 rounded-lg text-sm border border-slate-200 transition">Reset</a>
            @endif
        </div>
    </form>

    <h2 class="text-lg font-bold text-slate-800 pt-2">Tugas Aktif (Pending / Dalam Proses)</h2>
    <!-- Active Tasks List Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Tugas</th>
                        <th class="px-6 py-4">Karyawan Pelaksana</th>
                        <th class="px-6 py-4">Target Pekerjaan</th>
                        <th class="px-6 py-4">Batas Waktu</th>
                        <th class="px-6 py-4">Progres</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activeTasks as $task)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $task->judul_tugas }}</div>
                                @if($task->divisi)
                                    <div class="inline-flex mt-1 mb-0.5 px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100">{{ $task->divisi }}</div>
                                @endif
                                <div class="text-xs text-slate-400 mt-1 truncate max-w-xs">{{ $task->deskripsi }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <div class="font-medium">{{ $task->karyawan->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $task->karyawan->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $task->target }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium">
                                    {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                </div>
                                <div class="text-xs mt-0.5 {{ \Carbon\Carbon::parse($task->deadline)->isPast() ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                    {{ \Carbon\Carbon::parse($task->deadline)->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-navy-800 h-1.5 rounded-full" style="width: {{ $task->persentase }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 font-bold">{{ $task->persentase }}%</span>
                                </div>
                                @if($task->checklists->count() > 0)
                                    <div class="text-[10px] text-slate-500 font-medium mt-1">
                                        <i class="fa-solid fa-list-check text-navy-800 mr-0.5"></i> {{ $task->checklists->where('is_completed', true)->count() }}/{{ $task->checklists->count() }} Checklist
                                    </div>
                                @endif
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
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center space-x-1.5 whitespace-nowrap">
                                <button onclick="openEditModal({{ json_encode($task) }})" 
                                    class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold text-navy-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('manajer.tasks.destroy', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini beserta seluruh riwayat progresnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-2"></i>
                                <p>Tidak ada tugas aktif yang sedang berjalan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <h2 class="text-lg font-bold text-slate-800 pt-6">Tugas Selesai</h2>
    <!-- Completed Tasks List Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Tugas</th>
                        <th class="px-6 py-4">Karyawan Pelaksana</th>
                        <th class="px-6 py-4">Target Pekerjaan</th>
                        <th class="px-6 py-4">Batas Waktu</th>
                        <th class="px-6 py-4">Progres</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($completedTasks as $task)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $task->judul_tugas }}</div>
                                @if($task->divisi)
                                    <div class="inline-flex mt-1 mb-0.5 px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100">{{ $task->divisi }}</div>
                                @endif
                                <div class="text-xs text-slate-400 mt-1 truncate max-w-xs">{{ $task->deskripsi }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <div class="font-medium">{{ $task->karyawan->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $task->karyawan->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $task->target }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium">
                                    {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                </div>
                                <div class="text-xs mt-0.5 text-slate-400">
                                    Selesai
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $task->persentase }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 font-bold">{{ $task->persentase }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->status === 'Completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                        Menunggu Evaluasi
                                    </span>
                                @elseif($task->status === 'Reviewed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        Dievaluasi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center space-x-1.5 whitespace-nowrap">
                                <button onclick="openEditModal({{ json_encode($task) }})" 
                                    class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold text-navy-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('manajer.tasks.destroy', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini beserta seluruh riwayat progresnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-check-circle text-4xl mb-2"></i>
                                <p>Belum ada tugas yang selesai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CREATE TASK MODAL -->
<div id="create-modal" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-lg overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 bg-navy-800 text-white flex items-center justify-between">
            <h3 class="font-bold text-lg">Buat Tugas Baru</h3>
            <button onclick="toggleModal('create-modal')" class="text-white hover:text-red-200">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="{{ route('manajer.tasks.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="create_judul_tugas" class="block text-xs font-semibold text-slate-600 mb-1">Judul Tugas</label>
                <input type="text" id="create_judul_tugas" name="judul_tugas" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm"
                    placeholder="Contoh: Pembuatan Desain Dashboard Admin">
            </div>
            <div>
                <label for="create_deskripsi" class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi & Instruksi Tugas</label>
                <textarea id="create_deskripsi" name="deskripsi" rows="4" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm"
                    placeholder="Tuliskan petunjuk pengerjaan dan detail instruksi tugas..."></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="create_target" class="block text-xs font-semibold text-slate-600 mb-1">Target Pekerjaan</label>
                    <input type="text" id="create_target" name="target" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm"
                        placeholder="Contoh: Selesai 100% / 5 Desain">
                </div>
                <div>
                    <label for="create_deadline" class="block text-xs font-semibold text-slate-600 mb-1">Batas Waktu (Deadline)</label>
                    <input type="date" id="create_deadline" name="deadline" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="create_id_karyawan" class="block text-xs font-semibold text-slate-600 mb-1">Pilih Karyawan Pelaksana</label>
                    <select id="create_id_karyawan" name="id_karyawan" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                        <option value="" disabled selected>Pilih Karyawan...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-divisi="{{ $emp->divisi }}">{{ $emp->name }} ({{ $emp->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="create_divisi" class="block text-xs font-semibold text-slate-600 mb-1">Divisi / Departemen</label>
                    <select id="create_divisi" name="divisi" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                        <option value="" disabled selected>Pilih Divisi...</option>
                        <option value="IT & Software">IT & Software</option>
                        <option value="Marketing & Sales">Marketing & Sales</option>
                        <option value="Human Resources">Human Resources</option>
                        <option value="Finance & Accounting">Finance & Accounting</option>
                        <option value="Operations">Operations</option>
                        <option value="Design & Creative">Design & Creative</option>
                    </select>
                </div>
            </div>

            <!-- Checklist Subtugas -->
            <div class="border-t border-slate-100 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-slate-700">Checklist Poin Tugas (Subtugas)</label>
                    <button type="button" onclick="addChecklistItem('create-checklist-container')" class="text-xs text-navy-800 font-bold hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-plus-circle"></i> Tambah Item
                    </button>
                </div>
                <div id="create-checklist-container" class="space-y-2 max-h-36 overflow-y-auto pr-1">
                    <div class="flex items-center gap-2">
                        <input type="text" name="checklists[]" placeholder="Item checklist 1 (contoh: Analisis kebutuhan)" class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-slate-700 text-xs shadow-sm focus:border-navy-600 focus:outline-none">
                        <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-red-500 p-1"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                <button type="button" onclick="toggleModal('create-modal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-navy-800 hover:bg-navy-700 rounded-lg shadow-sm transition">
                    Delegasikan Tugas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT TASK MODAL -->
<div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-lg overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 bg-navy-800 text-white flex items-center justify-between">
            <h3 class="font-bold text-lg">Edit Detail Tugas</h3>
            <button onclick="toggleModal('edit-modal')" class="text-white hover:text-red-200">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form id="edit-form" action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_judul_tugas" class="block text-xs font-semibold text-slate-600 mb-1">Judul Tugas</label>
                <input type="text" id="edit_judul_tugas" name="judul_tugas" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
            </div>
            <div>
                <label for="edit_deskripsi" class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi & Instruksi Tugas</label>
                <textarea id="edit_deskripsi" name="deskripsi" rows="4" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm"></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit_target" class="block text-xs font-semibold text-slate-600 mb-1">Target Pekerjaan</label>
                    <input type="text" id="edit_target" name="target" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                </div>
                <div>
                    <label for="edit_deadline" class="block text-xs font-semibold text-slate-600 mb-1">Batas Waktu (Deadline)</label>
                    <input type="date" id="edit_deadline" name="deadline" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit_id_karyawan" class="block text-xs font-semibold text-slate-600 mb-1">Karyawan Pelaksana</label>
                    <select id="edit_id_karyawan" name="id_karyawan" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-divisi="{{ $emp->divisi }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_divisi" class="block text-xs font-semibold text-slate-600 mb-1">Divisi / Departemen</label>
                    <select id="edit_divisi" name="divisi" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                        <option value="IT & Software">IT & Software</option>
                        <option value="Marketing & Sales">Marketing & Sales</option>
                        <option value="Human Resources">Human Resources</option>
                        <option value="Finance & Accounting">Finance & Accounting</option>
                        <option value="Operations">Operations</option>
                        <option value="Design & Creative">Design & Creative</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="edit_status" class="block text-xs font-semibold text-slate-600 mb-1">Status Pengerjaan</label>
                <select id="edit_status" name="status" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                    <option value="Pending">Pending</option>
                    <option value="In Progress">Dalam Proses</option>
                    <option value="Completed">Completed (Selesai)</option>
                    <option value="Reviewed">Reviewed (Sudah Dievaluasi)</option>
                </select>
            </div>

            <!-- Checklist Subtugas -->
            <div class="border-t border-slate-100 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-slate-700">Checklist Poin Tugas (Subtugas)</label>
                    <button type="button" onclick="addChecklistItem('edit-checklist-container')" class="text-xs text-navy-800 font-bold hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-plus-circle"></i> Tambah Item
                    </button>
                </div>
                <div id="edit-checklist-container" class="space-y-2 max-h-36 overflow-y-auto pr-1">
                    <!-- Populated via Javascript -->
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                <button type="button" onclick="toggleModal('edit-modal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-navy-800 hover:bg-navy-700 rounded-lg shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }

    function addChecklistItem(containerId, value = '') {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="checklists[]" value="${value}" placeholder="Item checklist subtugas" class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-slate-700 text-xs shadow-sm focus:border-navy-600 focus:outline-none">
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-red-500 p-1"><i class="fa-solid fa-trash"></i></button>
        `;
        container.appendChild(div);
    }

    function openEditModal(task) {
        document.getElementById('edit_judul_tugas').value = task.judul_tugas;
        document.getElementById('edit_deskripsi').value = task.deskripsi;
        document.getElementById('edit_target').value = task.target;
        document.getElementById('edit_id_karyawan').value = task.id_karyawan;
        document.getElementById('edit_status').value = task.status;
        if(task.divisi) {
            document.getElementById('edit_divisi').value = task.divisi;
        }
        
        // Populate checklists
        const editContainer = document.getElementById('edit-checklist-container');
        editContainer.innerHTML = '';
        if (task.checklists && task.checklists.length > 0) {
            task.checklists.forEach(item => {
                addChecklistItem('edit-checklist-container', item.judul);
            });
        } else {
            addChecklistItem('edit-checklist-container');
        }

        // Format deadline date to YYYY-MM-DD
        if (task.deadline) {
            const date = new Date(task.deadline);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            document.getElementById('edit_deadline').value = `${year}-${month}-${day}`;
        }

        // Set action route for manajer
        const form = document.getElementById('edit-form');
        form.action = `/manajer/tasks/${task.id}`;

        toggleModal('edit-modal');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const setupAutoDivisi = (empSelectId, divisiSelectId) => {
            const empSelect = document.getElementById(empSelectId);
            const divisiSelect = document.getElementById(divisiSelectId);
            if (!empSelect || !divisiSelect) return;

            empSelect.addEventListener('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const divisi = selectedOpt ? selectedOpt.getAttribute('data-divisi') : '';
                if (divisi) {
                    let exists = Array.from(divisiSelect.options).some(opt => opt.value === divisi);
                    if (!exists) {
                        const newOpt = new Option(divisi, divisi);
                        divisiSelect.add(newOpt);
                    }
                    divisiSelect.value = divisi;
                }
            });
        };

        setupAutoDivisi('create_id_karyawan', 'create_divisi');
        setupAutoDivisi('edit_id_karyawan', 'edit_divisi');
    });
</script>
@endsection
