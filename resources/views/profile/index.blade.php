@extends('layouts.app')

@section('title', 'Pengaturan Profil')
@section('page_title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- User Card Summary -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row items-center gap-6">
        <div class="w-20 h-20 rounded-full bg-navy-800 text-white flex items-center justify-center text-3xl font-bold shadow">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="text-center md:text-left flex-1">
            <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ $user->email }}</p>
            <div class="mt-2 flex flex-wrap items-center justify-center md:justify-start gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-navy-50 text-navy-800 border border-navy-100 uppercase">
                    Role: {{ $user->role }}
                </span>
                @if($user->divisi)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                        Divisi: {{ $user->divisi }}
                    </span>
                @endif
            </div>
        </div>
        <div class="text-xs text-slate-400">
            Bergabung sejak {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success_profile'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-base"></i>
            <span>{{ session('success_profile') }}</span>
        </div>
    @endif

    @if(session('success_password'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-base"></i>
            <span>{{ session('success_password') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Update Profile Info Form -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 text-lg mb-1 flex items-center gap-2">
                <i class="fa-solid fa-user-gear text-navy-800"></i> Informasi Diri
            </h3>
            <p class="text-xs text-slate-500 mb-5">Perbarui nama dan alamat email terdaftar Anda.</p>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-navy-600 focus:ring-2 focus:ring-navy-100 outline-none @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-navy-600 focus:ring-2 focus:ring-navy-100 outline-none @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1">Role / Peran</label>
                    <input type="text" value="{{ ucfirst($user->role) }}" disabled
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-navy-800 hover:bg-navy-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition duration-150">
                        Simpan Perubahan Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password Form -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 text-lg mb-1 flex items-center gap-2">
                <i class="fa-solid fa-lock text-navy-800"></i> Ubah Kata Sandi
            </h3>
            <p class="text-xs text-slate-500 mb-5">Ganti kata sandi secara berkala untuk menjaga keamanan akun.</p>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-navy-600 focus:ring-2 focus:ring-navy-100 outline-none @error('current_password') border-red-500 @enderror"
                        placeholder="••••••••">
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-navy-600 focus:ring-2 focus:ring-navy-100 outline-none @error('password') border-red-500 @enderror"
                        placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-navy-600 focus:ring-2 focus:ring-navy-100 outline-none"
                        placeholder="Ulangi kata sandi baru">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition duration-150">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
