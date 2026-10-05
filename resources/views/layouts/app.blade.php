<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f4f2ee]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Performan-Ku</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for Dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        react: {
                            nav: '#1a2a24',
                            bg: '#21352f',
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
                            800: '#21352f', // Changed to react bg
                            900: '#1a2a24', // Changed to react nav
                            950: '#1d2d26',
                        }
                    }
                }
            }
        }
    </script>
    @yield('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100 overflow-hidden">
    <div class="flex h-screen w-full">
        <!-- Left Sidebar -->
        <aside class="w-64 bg-[#21352f] text-white flex-col hidden md:flex z-20 shrink-0 shadow-lg relative overflow-hidden">
            <!-- Sidebar decorative wave -->
            <div class="absolute bottom-0 left-0 w-full h-48 opacity-10 pointer-events-none">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full transform scale-150 origin-bottom-left">
                    <path fill="#ffffff" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.1,-46.3C90.4,-33.5,96,-18,94.3,-3.3C92.6,11.4,83.6,25.3,73.5,37.3C63.4,49.3,52.2,59.3,39.3,66.8C26.4,74.3,11.8,79.3,-3.1,84.1C-18.1,89,-33.4,93.6,-46.1,87.7C-58.8,81.8,-68.9,65.4,-77.2,50.1C-85.5,34.8,-92,20.6,-92,-0.2C-92,-21,-85.5,-40.5,-74.6,-54.6C-63.7,-68.7,-48.4,-77.4,-33.8,-82.1C-19.2,-86.8,-5.3,-87.5,9,-83.4C23.3,-79.3,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
                </svg>
            </div>
            
            <div class="h-16 flex items-center justify-center border-b border-white/5 bg-[#21352f] relative z-10">
                <a href="/" class="text-xl font-bold tracking-wider flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white">
                        <i class="fa-solid fa-leaf text-sm"></i>
                    </div>
                    Performan-Ku
                </a>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto relative z-10">
                @php
                    $activeRole = session('active_role', auth()->user()->role);
                    $displayRole = $activeRole === 'hr' || $activeRole === 'admin' ? 'HR / Admin' : $activeRole;
                @endphp
                @if($activeRole === 'admin' || $activeRole === 'hr')
                    <!-- Admin / HR Sidebar Links -->
                    <a href="{{ route('admin.dashboard') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('admin.dashboard') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-house w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.monitoring') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('admin.monitoring') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i>
                        <span>Monitoring Kinerja</span>
                    </a>
                    <a href="{{ route('admin.absensi') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('admin.absensi') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-clock w-5 text-center"></i>
                        <span>Rekap Absensi</span>
                    </a>
                    <a href="{{ route('admin.users') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('admin.users') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        <span>Karyawan</span>
                    </a>
                @elseif($activeRole === 'karyawan')
                    <!-- Karyawan Sidebar Links -->
                    <a href="{{ route('karyawan.dashboard') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('karyawan.dashboard') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-house w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('karyawan.absensi') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('karyawan.absensi') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-clock w-5 text-center"></i>
                        <span>Absensi</span>
                    </a>
                    <a href="{{ route('karyawan.tasks') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('karyawan.tasks') || Route::is('karyawan.task.detail') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-file-lines w-5 text-center"></i>
                        <span>Tugas</span>
                    </a>
                @elseif($activeRole === 'manajer')
                    <!-- Manajer Sidebar Links -->
                    <a href="{{ route('manajer.dashboard') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('manajer.dashboard') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-house w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('manajer.tasks') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('manajer.tasks') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-file-lines w-5 text-center"></i>
                        <span>Tugas</span>
                    </a>
                    <a href="{{ route('manajer.performance') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('manajer.performance') || Route::is('manajer.employee.detail') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        <span>Karyawan</span>
                    </a>
                    <a href="{{ route('manajer.evaluations') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('manajer.evaluations') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Riwayat Evaluasi</span>
                    </a>
                @endif

                <a href="{{ route('profile.index') }}" 
                    class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ Route::is('profile.index') ? 'bg-[#2a423a] text-white shadow-sm' : 'text-gray-400 hover:bg-[#2a423a]/50 hover:text-white' }}">
                    <i class="fa-solid fa-user-gear w-5 text-center"></i>
                    <span>Profil Saya</span>
                </a>

            </nav>

            <!-- Sidebar Footer / Logout -->
            <div class="p-4 border-t border-white/5 relative z-10">
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                    @csrf
                    <button type="submit" class="flex w-full items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-white/5 hover:text-white transition duration-150">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Right Side Panel (Header + Main Content) -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#f4f2ee]">
            <!-- Header Atas -->
            <header class="h-16 bg-[#fbfaf8] border-b border-gray-200 flex items-center justify-between px-6 z-10 shrink-0 text-gray-800">
                <!-- Mobile Navigation Toggle & Page Title -->
                <div class="flex items-center flex-1">
                    <button class="md:hidden text-gray-500 hover:text-[#4d7a6b] focus:outline-none mr-4" id="mobile-menu-button">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <!-- Search Bar (Visual Only) -->
                    <div class="hidden md:flex items-center bg-gray-100 rounded-full px-4 py-2 w-64 lg:w-96">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        <input type="text" placeholder="Cari karyawan, tugas, atau laporan..." class="bg-transparent border-none focus:outline-none text-sm ml-3 w-full text-gray-600 placeholder-gray-400">
                    </div>
                </div>

                <!-- User Profile Action & Notification Bell -->
                <div class="flex items-center space-x-5">
                    @php
                        $notifications = collect();
                        $user = auth()->user();
                        if ($user) {
                            if ($user->role === 'karyawan') {
                                $upcomingTasks = $user->assignedTasks()
                                    ->whereIn('status', ['Pending', 'In Progress'])
                                    ->get()
                                    ->filter(function($t) {
                                        return \Carbon\Carbon::parse($t->deadline)->diffInDays(now(), false) >= -2;
                                    });
                                foreach ($upcomingTasks as $t) {
                                    $days = (int) ceil(now()->diffInDays(\Carbon\Carbon::parse($t->deadline), false));
                                    $text = $days < 0 ? "Deadline tugas '{$t->judul_tugas}' telah lewat!" : ($days == 0 ? "Deadline tugas '{$t->judul_tugas}' HARI INI!" : "Deadline tugas '{$t->judul_tugas}' sisa {$days} hari!");
                                    $notifications->push([
                                        'icon' => 'fa-solid fa-hourglass-half text-amber-500',
                                        'title' => 'Peringatan Deadline',
                                        'text' => $text,
                                        'link' => route('karyawan.task.detail', $t->id),
                                        'time' => \Carbon\Carbon::parse($t->deadline)->format('d M')
                                    ]);
                                }
                            } elseif ($user->role === 'manajer') {
                                $pendingEval = \App\Models\Task::where('status', 'Completed')->count();
                                if ($pendingEval > 0) {
                                    $notifications->push([
                                        'icon' => 'fa-solid fa-file-signature text-blue-500',
                                        'title' => 'Tugas Perlu Evaluasi',
                                        'text' => "Ada {$pendingEval} tugas selesai yang menantikan evaluasi Anda.",
                                        'link' => route('manajer.dashboard'),
                                        'time' => 'Hari ini'
                                    ]);
                                }
                            } elseif ($user->role === 'admin' || $user->role === 'hr') {
                                $lateToday = \App\Models\Absensi::where('tanggal', now()->toDateString())->where('status', 'Terlambat')->count();
                                if ($lateToday > 0) {
                                    $notifications->push([
                                        'icon' => 'fa-solid fa-clock text-red-500',
                                        'title' => 'Keterlambatan Hari Ini',
                                        'text' => "Terdapat {$lateToday} karyawan terlambat absen hari ini.",
                                        'link' => route('admin.absensi'),
                                        'time' => 'Hari ini'
                                    ]);
                                }
                            }
                        }
                    @endphp

                    <!-- Notification Bell Dropdown -->
                    <div class="relative" id="notification-dropdown-container">
                        <button onclick="toggleNotificationDropdown()" class="text-gray-400 hover:text-[#4d7a6b] transition relative focus:outline-none p-1">
                            <i class="fa-regular fa-bell text-xl"></i>
                            @if($notifications->count() > 0)
                                <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-[#fbfaf8]"></span>
                            @endif
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-200 z-50 overflow-hidden">
                            <div class="px-4 py-3 bg-[#1a2a24] text-white flex items-center justify-between">
                                <span class="font-bold text-xs uppercase tracking-wider">Notifikasi</span>
                                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full font-semibold">{{ $notifications->count() }} Baru</span>
                            </div>
                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-100">
                                @forelse($notifications as $notif)
                                    <a href="{{ $notif['link'] }}" class="p-3.5 flex items-start space-x-3 hover:bg-slate-50 transition block">
                                        <div class="text-base mt-0.5 shrink-0">
                                            <i class="{{ $notif['icon'] }}"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-slate-800">{{ $notif['title'] }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5 leading-snug">{{ $notif['text'] }}</p>
                                            <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $notif['time'] }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-6 text-center text-slate-400">
                                        <i class="fa-regular fa-bell-slash text-2xl mb-1.5 block"></i>
                                        <p class="text-xs">Tidak ada notifikasi baru.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <script>
                        function toggleNotificationDropdown() {
                            const dropdown = document.getElementById('notification-dropdown');
                            dropdown.classList.toggle('hidden');
                        }

                        document.addEventListener('click', function(e) {
                            const container = document.getElementById('notification-dropdown-container');
                            if (container && !container.contains(e.target)) {
                                const dropdown = document.getElementById('notification-dropdown');
                                if (dropdown) dropdown.classList.add('hidden');
                            }
                        });
                    </script>

                    <a href="{{ route('profile.index') }}" title="Pengaturan Profil" class="text-gray-400 hover:text-[#4d7a6b] transition relative">
                        <i class="fa-solid fa-gear text-lg"></i>
                    </a>
                    <a href="{{ route('profile.index') }}" class="flex items-center space-x-3 hover:opacity-80 transition cursor-pointer">
                        <div class="h-9 w-9 rounded-full bg-[#1a2a24] text-white font-bold flex items-center justify-center uppercase text-sm shadow-sm">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="text-xs hidden sm:block">
                            <p class="font-bold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-gray-500 uppercase font-medium">{{ $displayRole }}</p>
                        </div>
                    </a>
                </div>
            </header>

            <!-- Mobile Drawer Menu (Hidden by default) -->
            <div class="md:hidden hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm" id="mobile-menu-drawer-backdrop"></div>
            <aside class="md:hidden hidden fixed top-0 bottom-0 left-0 w-64 bg-react-bg text-white flex-col z-50 transition-all duration-300 transform -translate-x-full" id="mobile-menu-drawer">
                <div class="h-16 flex items-center justify-between px-6 border-b border-navy-700 bg-react-nav/20">
                    <span class="text-lg font-bold tracking-wider">Performan-Ku</span>
                    <button class="text-white hover:text-red-200 focus:outline-none" id="mobile-menu-close">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                    <!-- Copy links for mobile -->
                    @if($activeRole === 'admin' || $activeRole === 'hr')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('admin.dashboard') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-chart-line w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.monitoring') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('admin.monitoring') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-chart-bar w-5 text-center"></i>
                            <span>Monitoring Kinerja</span>
                        </a>
                        <a href="{{ route('admin.absensi') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('admin.absensi') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-clock w-5 text-center"></i>
                            <span>Rekap Absensi</span>
                        </a>
                        <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('admin.users') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-users w-5 text-center"></i>
                            <span>Kelola Akun</span>
                        </a>
                    @elseif($activeRole === 'karyawan')
                        <a href="{{ route('karyawan.dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('karyawan.dashboard') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('karyawan.absensi') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('karyawan.absensi') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-clock w-5 text-center"></i>
                            <span>Absensi</span>
                        </a>
                        <a href="{{ route('karyawan.tasks') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('karyawan.tasks') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-tasks w-5 text-center"></i>
                            <span>Tugas Saya</span>
                        </a>
                    @elseif($activeRole === 'manajer')
                        <a href="{{ route('manajer.dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('manajer.dashboard') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('manajer.tasks') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('manajer.tasks') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-list-check w-5 text-center"></i>
                            <span>Kelola Tugas</span>
                        </a>
                        <a href="{{ route('manajer.performance') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('manajer.performance') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-chart-column w-5 text-center"></i>
                            <span>Kinerja Karyawan</span>
                        </a>
                        <a href="{{ route('manajer.evaluations') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ Route::is('manajer.evaluations') ? 'border-react-nav text-white' : 'text-blue-100 hover:border-react-nav' }}">
                            <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>
                            <span>Riwayat Evaluasi</span>
                        </a>
                    @endif
                </nav>
                <div class="p-4 border-t border-navy-700">
                    <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                        @csrf
                        <button type="submit" class="flex w-full items-center space-x-3 px-4 py-2 text-sm font-medium text-red-200 hover:bg-red-900/30 hover:text-white transition duration-150">
                            <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Container -->
            <main class="flex-1 overflow-y-auto p-6 focus:outline-none">
                <!-- Alerts (Flash Messages) -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm flex items-center shadow-sm">
                        <i class="fa-solid fa-circle-check text-emerald-500 mr-2 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-800 border border-red-200 text-sm flex items-center shadow-sm">
                        <i class="fa-solid fa-circle-xmark text-red-500 mr-2 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Dynamic Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toggle Drawer Script -->
    <script>
        const btnToggle = document.getElementById('mobile-menu-button');
        const drawer = document.getElementById('mobile-menu-drawer');
        const backdrop = document.getElementById('mobile-menu-drawer-backdrop');
        const btnClose = document.getElementById('mobile-menu-close');

        function toggleDrawer() {
            drawer.classList.toggle('hidden');
            drawer.classList.toggle('flex');
            backdrop.classList.toggle('hidden');
            setTimeout(() => {
                drawer.classList.toggle('-translate-x-full');
            }, 10);
        }

        if (btnToggle) {
            btnToggle.addEventListener('click', toggleDrawer);
            btnClose.addEventListener('click', toggleDrawer);
            backdrop.addEventListener('click', toggleDrawer);
        }
    </script>
    @yield('scripts')
</body>
</html>




