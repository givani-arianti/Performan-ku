<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Performan-Ku | Sistem Manajemen Kinerja Karyawan</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Caveat:wght@600&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        cursive: ['Caveat', 'cursive'],
                    },
                    colors: {
                        brand: {
                            dark: '#21352f',
                            primary: '#2d433b',
                            accent: '#4d7a6b',
                            light: '#e8f0ec',
                            bg: '#fbfaf8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #fbfaf8; overflow-x: hidden; }
        .blob-bg {
            position: absolute;
            z-index: -1;
        }
        .blob-1 { top: 0; right: 0; width: 40%; max-width: 600px; opacity: 0.1; }
        .blob-2 { bottom: -10%; left: -5%; width: 30%; max-width: 400px; opacity: 0.1; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
    </style>
</head>
<body class="text-gray-600 antialiased font-sans relative">

    <!-- Decorative Blobs -->
    <svg class="blob-bg blob-1 text-brand-primary" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path fill="currentColor" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.1,-46.3C90.4,-33.5,96,-18,94.3,-3.3C92.6,11.4,83.6,25.3,73.5,37.3C63.4,49.3,52.2,59.3,39.3,66.8C26.4,74.3,11.8,79.3,-3.1,84.1C-18.1,89,-33.4,93.6,-46.1,87.7C-58.8,81.8,-68.9,65.4,-77.2,50.1C-85.5,34.8,-92,20.6,-92,-0.2C-92,-21,-85.5,-40.5,-74.6,-54.6C-63.7,-68.7,-48.4,-77.4,-33.8,-82.1C-19.2,-86.8,-5.3,-87.5,9,-83.4C23.3,-79.3,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
    </svg>

    <!-- Header -->
    <header class="w-full top-0 z-50 bg-transparent py-5">
        <div class="container mx-auto px-6 lg:px-12 xl:px-20 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="text-2xl font-bold text-brand-dark flex items-center gap-2">
                <i class="fa-solid fa-leaf text-brand-accent"></i>
                Performan-Ku
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="#hero" class="text-brand-dark font-bold text-sm relative group">
                    Beranda
                    <span class="absolute -bottom-1.5 left-0 w-full h-0.5 bg-brand-accent rounded-full"></span>
                </a>
                <a href="#fitur" class="text-gray-500 hover:text-brand-dark text-sm font-medium transition">Fitur</a>
                <a href="#tentang" class="text-gray-500 hover:text-brand-dark text-sm font-medium transition">Tentang</a>
                <a href="#kontak" class="text-gray-500 hover:text-brand-dark text-sm font-medium transition">Kontak</a>
            </nav>

            <!-- Actions -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ route('karyawan.dashboard') }}" class="text-brand-dark text-sm font-semibold hover:text-brand-accent transition flex items-center gap-2">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2d433b&color=fff" class="w-8 h-8 rounded-full border border-gray-200" alt="Avatar">
                        {{ auth()->user()->name }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-brand-primary text-white px-6 py-2 rounded-full text-sm font-semibold hover:bg-brand-dark transition shadow-md">
                        Masuk Ke Sistem
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="relative pt-12 pb-24 lg:pt-20 lg:pb-32 overflow-visible">
        <div class="container mx-auto px-6 lg:px-12 xl:px-20">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-8">
                
                <!-- Left: Content -->
                <div class="w-full lg:w-5/12 z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-light border border-brand-accent/20 text-brand-dark text-xs font-semibold mb-6">
                        <i class="fa-solid fa-chart-line text-brand-accent"></i>
                        Sistem Monitoring & Analisis Performa Karyawan
                    </div>
                    
                    <h1 class="text-4xl lg:text-5xl xl:text-[3.5rem] font-bold text-brand-primary leading-[1.1] mb-6 tracking-tight">
                        Kelola Kinerja,<br>
                        Raih Target Bersama
                    </h1>
                    
                    <p class="text-gray-600 text-base leading-relaxed mb-8 max-w-lg">
                        Sistem yang membantu perusahaan dalam memantau performa karyawan, mengelola tugas, dan menganalisis pencapaian target kerja secara efektif dan terukur.
                    </p>
                    
                    @auth
                        <a href="{{ route('karyawan.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-full bg-brand-primary text-white font-medium hover:bg-brand-dark transition shadow-lg shadow-brand-primary/30 group">
                            Ke Dashboard <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-full bg-brand-primary text-white font-medium hover:bg-brand-dark transition shadow-lg shadow-brand-primary/30 group">
                            Mulai Sekarang <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    @endauth
                </div>

                <!-- Right: Image -->
                <div class="w-full lg:w-7/12 relative z-10">
                    <!-- Floating Cursive Text -->
                    <div class="absolute -top-8 -right-4 lg:-top-4 lg:-right-8 z-20 hidden md:block transform rotate-6">
                        <p class="font-cursive text-2xl text-brand-dark leading-tight text-right opacity-80">
                            Kinerja yang baik<br>berawal dari data<br>yang tepat.
                        </p>
                        <svg class="w-24 h-4 ml-auto text-brand-accent opacity-60" viewBox="0 0 100 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 15Q40 -5 95 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-brand-primary/10">
                        <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Office Workspace" class="w-full h-auto object-cover transform hover:scale-105 transition duration-700">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3 Features Cards (Overlapping Hero slightly) -->
    <section id="fitur" class="relative z-20 -mt-16 lg:-mt-24 mb-20">
        <div class="container mx-auto px-6 lg:px-12 xl:px-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 flex items-start gap-4 card-hover transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-brand-light flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-group text-brand-accent text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-brand-dark font-bold text-base mb-1">Monitoring Real-time</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Pantau progress tugas secara langsung</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 flex items-start gap-4 card-hover transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-clipboard-check text-blue-500 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-brand-dark font-bold text-base mb-1">Evaluasi Objektif</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Berbasis data dan indikator yang jelas</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 flex items-start gap-4 card-hover transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-chart-line text-purple-500 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-brand-dark font-bold text-base mb-1">Pengembangan Karir</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Dukung pengembangan potensi karyawan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="py-20 relative">
        <!-- Background decorative shape bottom right -->
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-brand-primary rounded-tl-full opacity-10 z-0"></div>
        <div class="container mx-auto px-6 lg:px-12 xl:px-20 relative z-10">
            <div class="flex flex-col lg:flex-row gap-16 lg:gap-12 items-center">
                
                <!-- Left Text -->
                <div class="w-full lg:w-5/12">
                    <div class="flex items-center gap-4 mb-4">
                        <span class="w-8 h-0.5 bg-brand-accent"></span>
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Tentang Sistem</span>
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-brand-dark leading-tight mb-6">
                        Solusi Modern untuk<br>Manajemen Kinerja Karyawan
                    </h2>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        PerformaKu dirancang untuk memudahkan HR, Manajer, dan Karyawan dalam mengelola tugas, memantau progres, serta mencapai target kerja dengan lebih transparan dan efisien.
                    </p>
                    <a href="#" class="text-brand-accent font-bold text-sm inline-flex items-center group">
                        Pelajari lebih lanjut <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <!-- Right Cards Grid -->
                <div class="w-full lg:w-7/12 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Untuk Karyawan -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-200/50 border border-gray-100 flex flex-col h-full card-hover">
                        <div class="w-12 h-12 rounded-full bg-brand-light flex items-center justify-center mb-6">
                            <i class="fa-solid fa-user text-brand-accent text-lg"></i>
                        </div>
                        <h4 class="font-bold text-brand-dark mb-3">Untuk Karyawan</h4>
                        <p class="text-sm text-gray-500 mb-6 flex-grow leading-relaxed">Lihat tugas, kerjakan checklist subtugas, dan pantau hasil evaluasi kinerja Anda.</p>
                        <button class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-brand-light hover:text-brand-accent transition">
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                    
                    <!-- Untuk HR -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-200/50 border border-gray-100 flex flex-col h-full card-hover">
                        <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center mb-6">
                            <i class="fa-solid fa-shield-halved text-orange-400 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-brand-dark mb-3">Untuk HR</h4>
                        <p class="text-sm text-gray-500 mb-6 flex-grow leading-relaxed">Kelola data karyawan, tugas, monitoring, dan laporan performa.</p>
                        <button class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-orange-50 hover:text-orange-400 transition">
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>
                    </div>

                    <!-- Untuk Manajer -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-200/50 border border-gray-100 flex flex-col h-full card-hover">
                        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center mb-6">
                            <i class="fa-solid fa-chart-pie text-purple-500 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-brand-dark mb-3">Untuk Manajer</h4>
                        <p class="text-sm text-gray-500 mb-6 flex-grow leading-relaxed">Pantau performa tim, evaluasi kinerja, dan berikan rekomendasi pengembangan.</p>
                        <button class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-purple-50 hover:text-purple-500 transition">
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer Simple -->
    <footer class="bg-brand-primary text-white py-8 text-center text-sm border-t-4 border-brand-accent">
        <p>&copy; {{ date('Y') }} Performan-Ku. All rights reserved.</p>
    </footer>

</body>
</html>
