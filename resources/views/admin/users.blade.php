@extends('layouts.app')

@section('title', 'Kelola Karyawan')
@section('page_title', 'Kelola Akun Pengguna')

@section('content')
<div class="space-y-6">
    <!-- Top Action bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">Kelola akun Karyawan dan Manajer yang dapat mengakses sistem.</p>
        </div>
        <button onclick="toggleModal('create-modal')" class="bg-navy-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg hover:bg-navy-700 shadow transition flex items-center self-start">
            <i class="fa-solid fa-user-plus mr-1.5"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Peran / Role</th>
                        <th class="px-6 py-4">Tanggal Bergabung</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                @if($user->divisi)
                                    <div class="text-[10px] text-blue-600 font-semibold mt-1 bg-blue-50 border border-blue-100 rounded px-1.5 py-0.5 inline-block">
                                        {{ $user->divisi }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'manajer')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                                        Manajer
                                    </span>
                                @elseif($user->role === 'karyawan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        Karyawan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $user->role }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <button onclick="openEditModal({{ json_encode($user) }})" 
                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-navy-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                </button>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait (tugas, progres, evaluasi) akan dihapus secara permanen.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                        <i class="fa-solid fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-4xl mb-2"></i>
                                <p>Belum ada data pengguna.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CREATE USER MODAL -->
<div id="create-modal" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-md overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 bg-navy-800 text-white flex items-center justify-between">
            <h3 class="font-bold text-lg">Tambah Pengguna Baru</h3>
            <button onclick="toggleModal('create-modal')" class="text-white hover:text-red-200">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="create_name" class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                <input type="text" id="create_name" name="name" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm"
                    placeholder="Nama Lengkap Karyawan / Manajer">
            </div>
            <div>
                <label for="create_email" class="block text-xs font-semibold text-slate-600 mb-1">Alamat Email</label>
                <input type="email" id="create_email" name="email" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm"
                    placeholder="email@perusahaan.com">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="create_role" class="block text-xs font-semibold text-slate-600 mb-1">Role / Peran</label>
                    <select id="create_role" name="role" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                        <option value="karyawan">Karyawan</option>
                        <option value="manajer">Manajer</option>
                    </select>
                </div>
                <div>
                    <label for="create_divisi" class="block text-xs font-semibold text-slate-600 mb-1">Divisi / Departemen</label>
                    <select id="create_divisi" name="divisi"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                        <option value="" selected>Pilih Divisi (Opsional)...</option>
                        <option value="IT & Software">IT & Software</option>
                        <option value="Marketing & Sales">Marketing & Sales</option>
                        <option value="Human Resources">Human Resources</option>
                        <option value="Finance & Accounting">Finance & Accounting</option>
                        <option value="Operations">Operations</option>
                        <option value="Design & Creative">Design & Creative</option>
                    </select>
                </div>
            </div>
            <div class="p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-xs flex items-start gap-2">
                <i class="fa-solid fa-info-circle mt-0.5 text-blue-500"></i>
                <div>
                    <strong>Info:</strong> Password awal untuk akun baru ini otomatis diset menjadi <strong>password123</strong>. Pengguna dapat mengubahnya nanti.
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                <button type="button" onclick="toggleModal('create-modal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-navy-800 hover:bg-navy-700 rounded-lg shadow-sm transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div id="edit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-md overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 bg-navy-800 text-white flex items-center justify-between">
            <h3 class="font-bold text-lg">Edit Detail Pengguna</h3>
            <button onclick="toggleModal('edit-modal')" class="text-white hover:text-red-200">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form id="edit-form" action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_name" class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                <input type="text" id="edit_name" name="name" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
            </div>
            <div>
                <label for="edit_email" class="block text-xs font-semibold text-slate-600 mb-1">Alamat Email</label>
                <input type="email" id="edit_email" name="email" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit_role" class="block text-xs font-semibold text-slate-600 mb-1">Role / Peran</label>
                    <select id="edit_role" name="role" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                        <option value="karyawan">Karyawan</option>
                        <option value="manajer">Manajer</option>
                    </select>
                </div>
                <div>
                    <label for="edit_divisi" class="block text-xs font-semibold text-slate-600 mb-1">Divisi / Departemen</label>
                    <select id="edit_divisi" name="divisi"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm">
                        <option value="">Pilih Divisi (Opsional)...</option>
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
                <label for="edit_password" class="block text-xs font-semibold text-slate-600 mb-1">Password Baru (Kosongkan jika tidak diganti)</label>
                <input type="password" id="edit_password" name="password"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-700 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-1 focus:ring-navy-600 text-sm"
                    placeholder="Minimal 8 karakter jika diganti">
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

    function openEditModal(user) {
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_divisi').value = user.divisi || '';
        document.getElementById('edit_password').value = '';
        
        // Update form action url dynamically
        const form = document.getElementById('edit-form');
        form.action = `/admin/users/${user.id}`;
        
        toggleModal('edit-modal');
    }
</script>
@endsection
