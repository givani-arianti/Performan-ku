<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Performan-Ku</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (via CDN for local compatibility stability and speed) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        react: {
                            nav: '#21332c',
                            bg: '#2d433b',
                            cyan: '#4d7a6b',
                            cyanHover: '#3d6356',
                        },
                        navy: {
                            50: '#f4f6fa',
                            100: '#e9edf5',
                            200: '#cbd7e7',
                            300: '#9cb5d3',
                            400: '#678dbb',
                            500: '#426fa1',
                            600: '#325785',
                            700: '#29466c',
                            800: '#2d433b',
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
            <div class="h-9 w-9 rounded-lg bg-react-nav flex items-center justify-center font-bold text-react-cyan text-lg shadow-sm">
                PK
            </div>
            <span class="text-xl font-extrabold tracking-tight text-react-nav">Performan-Ku</span>
        </a>

        <div class="text-center mb-5">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Selamat Datang</h2>
            <p class="text-sm text-slate-500 mt-1">Silakan masuk menggunakan akun terdaftar Anda.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="mb-4 p-3 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-sm">
                {{ session('info') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-800 border border-red-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form class="space-y-4" action="{{ route('login') }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-100 sm:text-sm @error('email') border-red-500 focus:ring-red-100 @enderror"
                    placeholder="nama@perusahaan.com">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 shadow-sm focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-100 sm:text-sm"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-slate-600">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-navy-800 focus:ring-navy-800">
                    <span class="ml-2">Ingat saya</span>
                </label>
            </div>

            <button type="submit"
                class="flex w-full justify-center rounded-xl bg-navy-800 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-600 focus:ring-offset-2 transition duration-150">
                Masuk
            </button>
        </form>

        <div class="mt-5 p-3 rounded-xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-600">
            <span class="font-semibold text-slate-700">Catatan:</span> Pendaftaran akun karyawan sepenuhnya dilakukan oleh <strong>HR</strong>. Silakan minta alamat email & password ke HR untuk dapat masuk.
        </div>

        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700 transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>


