<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Karyawan - Performan-Ku</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            50: '#f4f6fa',
                            100: '#e9edf5',
                            200: '#cbd7e7',
                            300: '#9cb5d3',
                            400: '#678dbb',
                            500: '#426fa1',
                            600: '#325785',
                            700: '#29466c',
                            800: '#0F2F64',
                            900: '#21332c',
                            950: '#1d2d26',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-100 font-sans antialiased flex items-center justify-center p-4 sm:p-6">
    <div class="w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-sm px-5 py-6 sm:px-7 sm:py-7">
        <a href="{{ url('/') }}" class="flex items-center justify-center space-x-2 mb-6 hover:opacity-80 transition cursor-pointer">
            <div class="h-9 w-9 rounded-lg bg-[#20232a] flex items-center justify-center font-bold text-[#61dafb] text-lg shadow-sm">
                PK
            </div>
            <span class="text-xl font-extrabold tracking-tight text-[#20232a]">Performan-Ku</span>
        </a>

        <div class="text-center mb-5">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Buat Akun</h2>
            <p class="text-sm text-slate-500 mt-1">Isi data di bawah untuk mendaftar.</p>
        </div>

        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
            <span><strong>HR/Manajer</strong> tidak perlu daftar. Langsung <a href="{{ route('login') }}" class="font-semibold underline">Login di sini</a>.</span>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 text-red-800 border border-red-200 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="space-y-4" action="{{ route('register.post') }}" method="POST">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-100 sm:text-sm @error('name') border-red-500 @enderror"
                    placeholder="Nama Lengkap Anda">
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-100 sm:text-sm @error('email') border-red-500 @enderror"
                    placeholder="nama@perusahaan.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <input id="password" name="password" type="password" required
                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-100 sm:text-sm"
                    placeholder="Minimal 8 karakter">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-100 sm:text-sm"
                    placeholder="Ulangi kata sandi">
            </div>

            <input type="hidden" name="role" value="karyawan">

            <button type="submit"
                class="flex w-full justify-center rounded-xl bg-navy-800 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-600 focus:ring-offset-2 transition duration-150">
                Daftar Sebagai Karyawan
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-navy-800 hover:text-navy-600 underline">Masuk di sini</a>
        </p>
        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700 transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>



