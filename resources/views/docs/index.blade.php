<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerobaks API Documentation</title>
    <link rel="icon" href="https://gerobaks.com/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Flowbite CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <!-- Swagger UI CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.17.14/swagger-ui.css">
    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    <script>
        // Tailwind Config
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {"50":"#ecfeff","100":"#cffafe","200":"#a5f3fc","300":"#67e8f9","400":"#22d3ee","500":"#06b6d4","600":"#0891b2","700":"#0e7490","800":"#155e75","900":"#164e63","950":"#083344"}
                    }
                },
                fontFamily: {
                    'body': ['Inter', 'ui-sans-serif', 'system-ui'],
                    'sans': ['Inter', 'ui-sans-serif', 'system-ui'],
                }
            }
        }
    </script>
    <style>
        .swagger-ui .topbar { display: none }
    <!-- Security Section -->
    <section id="security" class="py-8">
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl mb-12" data-aos="fade-up">
            <div class="p-6 md:p-8">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-rose-900/30 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-rose-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 0a8 8 0 1 1-16 0c0-6 8-14 8-14s8 8 8 14Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Security &amp; Encryption</h2>
                        <p class="text-sm text-slate-400">AES-256-CBC at rest, HTTPS in transit, and zero-trust defaults across critical flows.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="p-5 bg-slate-800/40 rounded-lg border border-slate-700" data-aos="fade-right" data-aos-delay="100">
                        <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full bg-rose-900/40 text-rose-200 border border-rose-700/50 mb-3">
                            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4a4 4 0 0 1 4 4v2h-8V8a4 4 0 0 1 4-4Zm8 10a4 4 0 0 1-4 4h-8a4 4 0 0 1-4-4v-2h16v2Z" />
                            </svg>
                            Data at Rest (AES-256-CBC)
                        </span>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Mulai September 2025, data sensitif seperti alamat order, catatan pelanggan, metode pembayaran, nomor referensi,
                            dan pesan percakapan akan disimpan menggunakan <strong class="text-rose-200">AES-256-CBC</strong> melalui custom cast Laravel.
                            Mekanisme ini kompatibel dengan <code class="px-1 py-0.5 bg-slate-900 rounded text-xs">APP_KEY</code> dan otomatis men-dekripsi saat data dibaca.
                        </p>
                        <ul class="mt-4 space-y-2 text-sm text-slate-300">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-400 mt-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7" />
                                </svg>
                                <div>
                                    <span class="font-medium text-white">Order::address_text &amp; notes</span>
                                    <p class="text-xs text-slate-400">Geolokasi pelanggan aman saat disimpan maupun tersinkronisasi.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-400 mt-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7" />
                                </svg>
                                <div>
                                    <span class="font-medium text-white">Payment::method &amp; reference</span>
                                    <p class="text-xs text-slate-400">Tokenisasi gateway tetap terlindungi meski database bocor.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-400 mt-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7" />
                                </svg>
                                <div>
                                    <span class="font-medium text-white">Chat::message</span>
                                    <p class="text-xs text-slate-400">Pesan antar pengguna dan mitra tetap privat di penyimpanan.</p>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-5 text-xs text-slate-400 bg-slate-900/60 border border-slate-800 rounded-lg p-4">
                            <div class="flex items-center gap-2 text-emerald-300 font-medium mb-2">
                                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12 10.243 16.243 18 8.486" />
                                </svg>
                                Enkripsi otomatis bekerja di seluruh resource API
                            </div>
                            <p class="leading-relaxed">
                                Tidak dibutuhkan perubahan dari sisi klien. Endpoint existing akan langsung menampilkan string yang sudah terdekripsi.
                                Untuk audit, gunakan query log yang sudah dimasking atau database view khusus.
                            </p>
                        </div>
                    </div>

                    <div class="p-5 bg-slate-800/40 rounded-lg border border-slate-700" data-aos="fade-left" data-aos-delay="200">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 11 3-3m0 0-3-3m3 3H9a6 6 0 0 0-6 6v5" />
                            </svg>
                            Laravel Implementation Snippet
                        </h3>
                        <pre class="text-xs md:text-sm bg-slate-950 border border-slate-800 rounded-lg p-4 overflow-x-auto"><code class="language-php">namespace App\Casts;
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .bg-grid {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-200">
                        <div class="mt-4" id="encryption-accordion" data-accordion="collapse">
                            <h3>
                                <button type="button" class="flex items-center justify-between w-full p-4 font-medium text-left text-sm text-slate-200 rounded-lg border border-slate-700 hover:bg-slate-800" data-accordion-target="#aes-panel" aria-expanded="false" aria-controls="aes-panel">
                                    <span>Operational &amp; Compliance Checklist</span>
                                    <svg data-accordion-icon class="w-3 h-3 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                    </svg>
                                </button>
                            </h3>
                            <div id="aes-panel" class="hidden" aria-labelledby="aes-heading">
                                <div class="p-4 text-xs text-slate-300 border border-t-0 border-slate-700 rounded-b-lg space-y-3">
                                    <div class="flex items-start gap-2">
                                        <span class="mt-0.5 inline-flex items-center justify-center w-5 h-5 text-[10px] font-semibold rounded-full bg-primary-500/20 text-primary-200 border border-primary-500/30">1</span>
                                        <p><strong>Rotate APP_KEY</strong> secara berkala lalu jalankan <code class="bg-slate-900 px-1 py-0.5 rounded">php artisan gerobaks:aes:rekey</code> (command tersedia di backlog).</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="mt-0.5 inline-flex items-center justify-center w-5 h-5 text-[10px] font-semibold rounded-full bg-primary-500/20 text-primary-200 border border-primary-500/30">2</span>
                                        <p>Aktifkan <strong>database activity auditing</strong> untuk tabel <em>orders</em>, <em>payments</em>, dan <em>chats</em>.</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="mt-0.5 inline-flex items-center justify-center w-5 h-5 text-[10px] font-semibold rounded-full bg-primary-500/20 text-primary-200 border border-primary-500/30">3</span>
                                        <p>Sinkronkan perubahan ini dengan aplikasi mobile agar tetap menangani data yang sudah terdekripsi secara transparan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- Navbar -->
<nav class="bg-slate-900/70 backdrop-blur-lg border-b border-slate-800/60 px-4 py-2.5 fixed left-0 right-0 top-0 z-50">
    <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
        <a href="#" class="flex items-center">
            <img src="https://flowbite.com/docs/images/logo.svg" class="mr-3 h-6 sm:h-8" alt="Gerobaks Logo" />
            <span class="self-center text-xl font-semibold whitespace-nowrap text-white">Gerobaks API</span>
        </a>
        <div class="flex items-center lg:order-2">
            <div id="environment-switcher" class="mr-2">
                <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-white bg-slate-800 hover:bg-slate-700 focus:ring-2 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center" type="button">
                    <span id="current-env">Local</span>
                    <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <div id="dropdown" class="z-10 hidden bg-slate-800 divide-y divide-slate-700 rounded-lg shadow w-60">
                    <ul class="py-2 text-sm" aria-labelledby="dropdownDefaultButton">
                        @foreach($servers as $server)
                            <li>
                                <button type="button" data-env-key="{{ $server['key'] }}" data-env-url="{{ $server['url'] }}" data-env-docs="{{ $server['docs'] }}" data-env-label="{{ $server['label'] }}" class="environment-option w-full px-4 py-3 hover:bg-slate-700 flex items-start text-left">
                                    <div>
                                        <div class="font-medium text-white">{{ $server['label'] }}</div>
                                        <div class="text-xs text-slate-400 mt-1">{{ $server['description'] }}</div>
                                    </div>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button id="theme-toggle" type="button" class="text-slate-400 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-700 rounded-lg text-sm p-2.5">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>
        </div>
        <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
            <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                <li>
                    <a href="#overview" class="block py-2 pr-4 pl-3 text-white border-b border-slate-700 hover:bg-slate-800 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-400 lg:p-0">Overview</a>
                </li>
                <li>
                    <a href="#quickstart" class="block py-2 pr-4 pl-3 text-white border-b border-slate-700 hover:bg-slate-800 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-400 lg:p-0">Quick Start</a>
                </li>
                <li>
                    <a href="#swagger" class="block py-2 pr-4 pl-3 text-white border-b border-slate-700 hover:bg-slate-800 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-400 lg:p-0">API Explorer</a>
                </li>
                <li>
                    <a href="#changelog" class="block py-2 pr-4 pl-3 text-white border-b border-slate-700 hover:bg-slate-800 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-400 lg:p-0">Changelog</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="pt-24 pb-24 bg-slate-900 bg-grid relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-primary-950/50 to-slate-950/90 z-0"></div>
    <div class="max-w-screen-xl mx-auto px-4 relative z-10">
        <div class="flex flex-col items-center justify-center text-center" data-aos="fade-up">
            <span class="hero-badge inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-primary-900/40 text-primary-300 border border-primary-800 mb-4">
                <svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
                </svg>
                Gerobaks Backend Platform
            </span>
            <h1 class="hero-title mb-4 text-5xl font-extrabold tracking-tight leading-tight text-white md:text-6xl">Backend &amp; API Documentation</h1>
            <p class="hero-subtitle max-w-2xl mb-6 font-light text-slate-300 lg:mb-8 md:text-lg">
                <strong class="text-primary-300">ID:</strong> Pusat informasi resmi mengenai arsitektur backend, panduan integrasi API, dan catatan perubahan produk.
                <br class="my-2">
                <strong class="text-primary-300">EN:</strong> Central hub for backend architecture, API integration guide, and detailed changelog.
            </p>
            
            <!-- Hero buttons -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4 mt-2">
                <a href="#swagger" class="hero-button inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-900">
                    Explore API
                    <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg>
                </a>
                <a href="#" id="copy-api-url" class="hero-button py-3 px-5 sm:ms-4 text-base font-medium text-slate-900 focus:outline-none bg-white rounded-lg border border-slate-200 hover:bg-slate-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-slate-200 dark:focus:ring-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:text-white dark:hover:bg-slate-700">
                    <span id="copy-button-text">Copy API URL</span>
                </a>
            </div>
        </div>

        <!-- Floating graphics for visual interest -->
    <div class="floating-orb absolute top-1/4 right-[10%] w-24 h-24 bg-primary-500/10 rounded-full blur-2xl"></div>
    <div class="floating-orb absolute bottom-1/3 left-[15%] w-32 h-32 bg-primary-600/10 rounded-full blur-3xl"></div>
    <div class="floating-orb absolute top-20 left-[30%] animate-float hidden md:block">
            <svg xmlns="http://www.w3.org/2000/svg" width="54" height="54" fill="none" viewBox="0 0 24 24" stroke="rgba(56, 189, 248, 0.25)" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
            </svg>
        </div>
    </div>
</header>
<!-- Main Content -->
<main class="max-w-screen-xl mx-auto px-4 pt-6">
    <!-- Overview Section -->
    <section id="overview" class="pt-16 pb-8">
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl mb-12" data-aos="fade-up">
            <div class="p-6 md:p-8">
                <div class="flex items-center mb-5">
                    <div class="p-2 bg-primary-900/30 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-primary-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6C4.9 6 2 12 2 12s2.9 6 10 6 10-6 10-6-2.9-6-10-6Z"/>
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Platform Capabilities</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="p-6 bg-slate-800/40 rounded-lg border border-slate-700/40" data-aos="fade-right" data-aos-delay="100">
                        <h3 class="text-xl font-semibold text-white mb-3">🇮🇩 Pengelolaan Sampah Pintar</h3>
                        <p class="text-slate-300">
                            Backend Gerobaks mendukung autentikasi berbasis token, manajemen jadwal penjemputan, pelacakan armada, notifikasi real-time,
                            percakapan pengguna-mitra, sistem saldo poin, serta penilaian layanan. Semua layanan dirancang untuk terintegrasi
                            mulus dengan aplikasi Flutter Gerobaks maupun kanal internal.
                        </p>
                        
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-primary-900/40 text-primary-300 border border-primary-800">Authentication</span>
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-900/40 text-emerald-300 border border-emerald-800">Scheduling</span>
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-amber-900/40 text-amber-300 border border-amber-800">Tracking</span>
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-pink-900/40 text-pink-300 border border-pink-800">Notifications</span>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-slate-800/40 rounded-lg border border-slate-700/40" data-aos="fade-left" data-aos-delay="200">
                        <h3 class="text-xl font-semibold text-white mb-3">🇬🇧 Intelligent Waste Lifecycle</h3>
                        <p class="text-slate-300">
                            The backend offers token-based authentication, scheduling, live fleet tracking, real-time notifications, in-app messaging,
                            reward ledger, and service ratings. Every endpoint is optimized for the Flutter client and ready for future channels such
                            as internal dashboards.
                        </p>
                        
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-violet-900/40 text-violet-300 border border-violet-800">Messaging</span>
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-900/40 text-blue-300 border border-blue-800">Points</span>
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-orange-900/40 text-orange-300 border border-orange-800">Ratings</span>
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-sky-900/40 text-sky-300 border border-sky-800">Reports</span>
                        </div>
                    </div>
                </div>

                <!-- Feature highlights -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card p-4 bg-slate-800/30 rounded-lg border border-slate-700/30 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-primary-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v14M9 5v14M4 5h16c.6 0 1 .4 1 1v12c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V6c0-.6.4-1 1-1Z"/>
                        </svg>
                        <h4 class="font-medium text-white">RESTful API</h4>
                    </div>
                    <div class="feature-card p-4 bg-slate-800/30 rounded-lg border border-slate-700/30 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 9A6 6 0 1 0 12 15v2.4m0 0a2.6 2.6 0 1 1-2.6 2.6m2.6-2.6a2.6 2.6 0 0 0-2.6 2.6m8.6-12h1M12 5V3m-9 9h2"/>
                        </svg>
                        <h4 class="font-medium text-white">Real-time Events</h4>
                    </div>
                    <div class="feature-card p-4 bg-slate-800/30 rounded-lg border border-slate-700/30 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-amber-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-8-6h16"/>
                        </svg>
                        <h4 class="font-medium text-white">OAuth2 Secure</h4>
                    </div>
                    <div class="feature-card p-4 bg-slate-800/30 rounded-lg border border-slate-700/30 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-pink-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.5 14.9C20 18 19 19 19 19l-2.8.7c-.9.2-1.7-.4-1.9-1.3L14 17H8l-.3 1.4c-.2.9-1.1 1.5-1.9 1.3L3 19s-1-1-1.5-4.1c-.5-3.1.5-13.8.5-13.8C2 .9 2.3.7 2.8.7L20.5.5C21 .5 21.4.9 21.3 1.2c0 0 .7 10.7.3 13.7Z"/>
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 14v-4s-1-1-3-1-3 1-3 1v4"/>
                        </svg>
                        <h4 class="font-medium text-white">Mobile Optimized</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quickstart Section -->
    <section id="quickstart" class="py-8">
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl mb-12" data-aos="fade-up">
            <div class="p-6 md:p-8">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-emerald-900/30 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 8H4m13-3H4m13 9H4m4 3h12m-9 5h6"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Quick Start Guide</h2>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="p-5 bg-slate-800/40 rounded-lg border border-slate-700" data-aos="fade-right" data-aos-delay="100">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 mr-2 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.5V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h7a2 2 0 0 1 2 2v.5M15 6v4m0 0 2-2m-2 2-2-2m7-4v16"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-white">🇮🇩 Langkah Lokal</h3>
                        </div>
                        
                        <div class="mb-4 relative">
                            <div class="absolute right-2 top-2">
                                <button type="button" class="copy-code-button text-xs p-1.5 bg-slate-700/70 hover:bg-slate-600 text-slate-300 rounded" data-clipboard-target="#local-setup-code">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M8 5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2V5Zm0 0h8"/>
                                    </svg>
                                </button>
                            </div>
                            <pre id="local-setup-code" class="text-xs md:text-sm bg-slate-950 p-4 rounded-lg overflow-x-auto text-slate-300">git clone https://github.com/aji-aali/gerobaks-api.git
cd gerobaks-api
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve</pre>
                        </div>
                        
                        <div class="text-sm text-slate-300">
                            <p class="mb-2">
                                Gunakan <code class="text-xs bg-slate-800 px-1 py-0.5 rounded">php artisan queue:work</code> untuk memproses job dan 
                                <code class="text-xs bg-slate-800 px-1 py-0.5 rounded">php artisan schedule:work</code> untuk menjalankan scheduler lokal.
                            </p>
                            <p>
                                Pastikan variabel <code class="text-xs bg-slate-800 px-1 py-0.5 rounded">API_BASE_URL</code> pada aplikasi Flutter menunjuk ke server ini.
                            </p>
                        </div>

                        <!-- Local testing tool -->
                        <div class="mt-4 p-3 bg-slate-800 rounded border border-slate-700">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 10 2 2 4-4m-5 9a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                                </svg>
                                <h4 class="text-sm font-medium text-white">Test Health Check</h4>
                            </div>
                            <div class="relative">
                                <input type="text" id="health-check-url" value="http://localhost:8000/api/health" class="bg-slate-900 border border-slate-700 text-slate-300 text-xs rounded block w-full p-2 focus:ring-primary-500 focus:border-primary-500">
                                <button id="test-health-button" class="absolute inset-y-0 right-0 px-3 py-1.5 text-xs font-medium text-white bg-primary-700 rounded-r border border-primary-700 hover:bg-primary-800 focus:ring-2 focus:outline-none focus:ring-primary-300">
                                    Test
                                </button>
                            </div>
                            <div id="health-result" class="mt-2 hidden text-xs p-2 rounded"></div>
                        </div>
                    </div>
                    
                    <div class="p-5 bg-slate-800/40 rounded-lg border border-slate-700" data-aos="fade-left" data-aos-delay="200">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 mr-2 text-primary-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-white">🇬🇧 Production Notes</h3>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="p-3 bg-slate-800 rounded border border-slate-700">
                                <h4 class="text-sm font-medium text-white mb-2">Security</h4>
                                <ul class="space-y-1 text-xs text-slate-400">
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Enable HTTPS
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Configure Sanctum
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Rotate secrets
                                    </li>
                                </ul>
                            </div>
                            <div class="p-3 bg-slate-800 rounded border border-slate-700">
                                <h4 class="text-sm font-medium text-white mb-2">Performance</h4>
                                <ul class="space-y-1 text-xs text-slate-400">
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Cache routes
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Cache config
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Use Redis
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="p-3 bg-slate-800 rounded border border-slate-700">
                                <h4 class="text-sm font-medium text-white mb-2">Background Jobs</h4>
                                <ul class="space-y-1 text-xs text-slate-400">
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Configure Supervisord
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Set up Horizon
                                    </li>
                                </ul>
                            </div>
                            <div class="p-3 bg-slate-800 rounded border border-slate-700">
                                <h4 class="text-sm font-medium text-white mb-2">Monitoring</h4>
                                <ul class="space-y-1 text-xs text-slate-400">
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Set up Sentry
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-3 h-3 mt-0.5 mr-1.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Configure NewRelic
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Environment tools -->
                        <div class="mt-5">
                            <div class="flex items-center justify-between mb-2.5">
                                <h4 class="text-sm font-medium text-white">Configuration Checklist</h4>
                                <span id="env-status-badge" class="bg-amber-900/50 text-amber-300 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Pending</span>
                            </div>
                            <ul class="space-y-2">
                                <li>
                                    <div class="flex items-center">
                                        <input id="env-check-1" type="checkbox" class="w-4 h-4 text-primary-600 bg-slate-700 border-slate-600 rounded focus:ring-primary-600">
                                        <label for="env-check-1" class="ms-2 text-xs font-medium text-slate-300">Environment variables configured</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <input id="env-check-2" type="checkbox" class="w-4 h-4 text-primary-600 bg-slate-700 border-slate-600 rounded focus:ring-primary-600">
                                        <label for="env-check-2" class="ms-2 text-xs font-medium text-slate-300">Database migrations complete</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <input id="env-check-3" type="checkbox" class="w-4 h-4 text-primary-600 bg-slate-700 border-slate-600 rounded focus:ring-primary-600">
                                        <label for="env-check-3" class="ms-2 text-xs font-medium text-slate-300">Queue worker running</label>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Environment playground -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-white">Environment Playground</h4>
                                <span class="text-xs text-slate-400">Choose a server and sync Swagger base URL</span>
                            </div>
                            <div id="server-card-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($servers as $server)
                                    <div class="server-card relative overflow-hidden p-4 bg-slate-900/60 border border-slate-700/60 rounded-lg transition duration-300" data-env-key="{{ $server['key'] }}" data-env-url="{{ $server['url'] }}" data-env-label="{{ $server['label'] }}" data-env-docs="{{ $server['docs'] }}">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <div class="text-sm font-semibold text-white">{{ $server['label'] }}</div>
                                                <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $server['description'] }}</p>
                                            </div>
                                            <span class="server-status-badge inline-flex items-center gap-1 text-[11px] font-medium px-2 py-1 rounded-full bg-slate-800 text-slate-300">Idle</span>
                                        </div>
                                        <div class="space-y-2 text-xs text-slate-400">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5 text-primary-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9-5 9 5-9 5-9-5Zm9 5v10" />
                                                </svg>
                                                <span class="truncate" title="{{ $server['url'] }}">{{ $server['url'] }}</span>
                                            </div>
                                            @if(!empty($server['docs']))
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                                                    </svg>
                                                    <a href="{{ $server['docs'] }}" target="_blank" rel="noopener" class="underline decoration-dotted text-emerald-300 hover:text-emerald-200">Docs</a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-4 flex items-center gap-2">
                                            <button type="button" class="select-server-button inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded border border-primary-500 text-primary-300 hover:bg-primary-600/20 transition" data-env-key="{{ $server['key'] }}" data-env-url="{{ $server['url'] }}" data-env-label="{{ $server['label'] }}" data-health-url="{{ rtrim($server['url'], '/') }}/api/health">
                                                Use Server
                                            </button>
                                            <button type="button" class="ping-server-button inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded border border-slate-600 text-slate-300 hover:bg-slate-700/50 transition" data-env-key="{{ $server['key'] }}" data-health-url="{{ rtrim($server['url'], '/') }}/api/health">
                                                Ping
                                            </button>
                                        </div>
                                        <div class="ping-result mt-3 hidden text-[11px] px-2 py-1 rounded border"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Swagger UI Section -->
    <section id="swagger" class="py-8">
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl mb-12" data-aos="fade-up">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="p-2 bg-amber-900/30 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-amber-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-white">Interactive API Explorer</h2>
                    </div>
                    
                    <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-3">
                        <a href="{{ $specUrl }}" target="_blank" rel="noopener" class="inline-flex justify-center items-center px-3 py-2 text-xs font-medium text-slate-200 rounded border border-slate-700 bg-slate-800 hover:bg-slate-700">
                            <svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7h8v8M5 12l-4 4m4-4 4 4m-4-4V5"/>
                            </svg>
                            openapi.yaml
                        </a>
                        
                        <button id="auth-button" type="button" class="inline-flex justify-center items-center px-3 py-2 text-xs font-medium text-slate-900 rounded border border-primary-600 bg-primary-500 hover:bg-primary-600 focus:ring-2 focus:ring-primary-300">
                            <svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1H7a1 1 0 0 1-1-1v-7c0-.6.4-1 1-1Z"/>
                            </svg>
                            Authorize
                        </button>
                    </div>
                </div>
                
                <p class="mb-6 text-sm text-slate-300 border-l-4 border-amber-500 pl-3 py-2 bg-slate-800/50">
                    <strong class="text-primary-300">ID:</strong> Jelajahi seluruh endpoint melalui Swagger UI di bawah ini atau akses langsung spesifikasi mentah via
                    <a href="{{ $specUrl }}" target="_blank" rel="noopener" class="underline text-primary-400">openapi.yaml</a>.
                    <br>
                    <strong class="text-primary-300">EN:</strong> Explore every endpoint using the embedded Swagger UI or fetch the raw specification at
                    <a href="{{ $specUrl }}" target="_blank" rel="noopener" class="underline text-primary-400">openapi.yaml</a>.
                </p>
                
                <!-- Auth Modal -->
                <div id="auth-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-slate-900 rounded-lg shadow border border-slate-700">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 border-b border-slate-700">
                                <h3 class="text-lg font-medium text-white">
                                    API Authorization
                                </h3>
                                <button type="button" class="text-slate-400 bg-transparent hover:bg-slate-800 hover:text-white rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="auth-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-6">
                                <div>
                                    <label for="bearer-token" class="block mb-2 text-sm font-medium text-white">Bearer Token</label>
                                    <input type="text" id="bearer-token" class="bg-slate-800 border border-slate-700 text-slate-300 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI...">
                                    <p class="mt-1 text-xs text-slate-500">Enter your bearer token for authentication</p>
                                </div>
                                
                                <!-- Test login -->
                                <div class="bg-slate-800 p-3 rounded-lg">
                                    <h4 class="text-sm font-medium text-white mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-primary-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18v-2a4 4 0 0 0-4-4h-4a4 4 0 0 0-4 4v2"/>
                                        </svg>
                                        Test Login
                                    </h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label for="test-email" class="block mb-1 text-xs font-medium text-slate-400">Email</label>
                                            <input type="email" id="test-email" value="demo@gerobaks.com" class="bg-slate-900 border border-slate-700 text-slate-300 text-xs rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2">
                                        </div>
                                        <div>
                                            <label for="test-password" class="block mb-1 text-xs font-medium text-slate-400">Password</label>
                                            <input type="password" id="test-password" value="password" class="bg-slate-900 border border-slate-700 text-slate-300 text-xs rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2">
                                        </div>
                                    </div>
                                    <button id="test-login-button" class="w-full mt-2 text-slate-900 bg-primary-500 hover:bg-primary-600 focus:ring-2 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-xs px-3 py-2 text-center">
                                        Generate Token
                                    </button>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="flex items-center p-6 space-x-2 border-t border-slate-700">
                                <button id="apply-auth-button" data-modal-hide="auth-modal" type="button" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-2 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Apply</button>
                                <button data-modal-hide="auth-modal" type="button" class="text-slate-300 bg-slate-700 hover:bg-slate-600 focus:ring-2 focus:outline-none focus:ring-slate-500 rounded-lg border border-slate-500 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Swagger UI container -->
                <div id="swagger-ui" class="bg-slate-950 rounded-lg border border-slate-800 overflow-hidden min-h-[650px]"></div>
            </div>
        </div>
    </section>

    <!-- Changelog Section -->
    <section id="changelog" class="py-8">
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl mb-12" data-aos="fade-up">
            <div class="p-6 md:p-8">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-violet-900/30 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-violet-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h13M9 12h13M9 17h13M4 7h1m-1 5h1m-1 5h1"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Release Notes</h2>
                </div>
                
                <!-- Version filter -->
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="text-sm text-slate-400">Filter by version:</span>
                    <button type="button" class="version-filter-btn px-3 py-1 text-xs font-medium rounded-full border border-violet-600 bg-violet-900/20 text-violet-300" data-version="all">All</button>
                    <button type="button" class="version-filter-btn px-3 py-1 text-xs font-medium rounded-full border border-slate-700 bg-slate-800 text-slate-300" data-version="v1">v1.x</button>
                    <button type="button" class="version-filter-btn px-3 py-1 text-xs font-medium rounded-full border border-slate-700 bg-slate-800 text-slate-300" data-version="v2">v2.x</button>
                </div>
                
                <div class="changelog prose prose-invert max-w-none prose-headings:text-white prose-a:text-primary-400 prose-code:text-emerald-300 prose-pre:bg-slate-800 prose-pre:border prose-pre:border-slate-700">
                    {!! $changelogHtml !!}
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 border-t border-slate-800/40">
        <div class="mx-auto max-w-screen-xl">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <a href="/" class="flex items-center">
                        <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 me-3" alt="Gerobaks Logo" />
                        <span class="self-center text-2xl font-semibold whitespace-nowrap text-white">Gerobaks</span>
                    </a>
                    <p class="mt-3 max-w-lg text-sm text-slate-400">
                        Platform manajemen sampah pintar yang menghubungkan warga dengan petugas kebersihan untuk lingkungan yang lebih bersih dan berkelanjutan.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                    <div>
                        <h2 class="mb-6 text-sm font-semibold uppercase text-white">Resources</h2>
                        <ul class="text-slate-400 font-medium">
                            <li class="mb-4"><a href="#overview" class="hover:underline">Product Overview</a></li>
                            <li class="mb-4"><a href="#quickstart" class="hover:underline">Quick Start Guide</a></li>
                            <li><a href="{{ $specUrl }}" target="_blank" rel="noopener" class="hover:underline">OpenAPI Specification</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold uppercase text-white">Follow us</h2>
                        <ul class="text-slate-400 font-medium">
                            <li class="mb-4"><a href="https://github.com/aji-aali/gerobaks" class="hover:underline">GitHub</a></li>
                            <li><a href="mailto:dev@gerobaks.com" class="hover:underline">Contact Team</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold uppercase text-white">Legal</h2>
                        <ul class="text-slate-400 font-medium">
                            <li class="mb-4"><a href="#" class="hover:underline">Privacy Policy</a></li>
                            <li><a href="#" class="hover:underline">Terms &amp; Conditions</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-slate-800 sm:mx-auto lg:my-8" />
            <div class="sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-slate-400 sm:text-center">
                    &copy; {{ now()->year }} <a href="https://gerobaks.com" class="hover:underline">Gerobaks</a>. All rights reserved.
                </span>
                <div class="flex mt-4 sm:justify-center sm:mt-0 space-x-5">
                    <a href="https://github.com/aji-aali/gerobaks" class="text-slate-500 hover:text-white">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 .333A9.911 9.911 0 0 0 6.866 19.65c.5.092.678-.215.678-.477 0-.237-.01-1.017-.014-1.845-2.757.6-3.338-1.169-3.338-1.169a2.627 2.627 0 0 0-1.1-1.451c-.9-.615.07-.6.07-.6a2.084 2.084 0 0 1 1.518 1.021 2.11 2.11 0 0 0 2.884.823c.044-.503.268-.973.63-1.325-2.2-.25-4.516-1.1-4.516-4.9A3.832 3.832 0 0 1 4.7 7.068a3.56 3.56 0 0 1 .095-2.623s.832-.266 2.726 1.016a9.409 9.409 0 0 1 4.962 0c1.89-1.282 2.717-1.016 2.717-1.016.366.83.402 1.768.1 2.623a3.827 3.827 0 0 1 1.02 2.659c0 3.807-2.319 4.644-4.525 4.889a2.366 2.366 0 0 1 .673 1.834c0 1.326-.012 2.394-.012 2.72 0 .263.18.572.681.475A9.911 9.911 0 0 0 10 .333Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="sr-only">GitHub</span>
                    </a>
                    <a href="mailto:dev@gerobaks.com" class="text-slate-500 hover:text-white">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                            <path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.979 1.979 0 0 0 .67.525l9.258 7.79.108-.037Z"/>
                            <path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"/>
                        </svg>
                        <span class="sr-only">Contact</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</main>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.17.14/swagger-ui-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/motion@11.11.0/dist/motion.min.js" integrity="sha384-qjctTy3nnf8D7Pdk1AmrhtgTWbvB2AVzXCRgOJYd0KV+PLyMAi3rYk/JjGrx95Ta" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" integrity="sha384-NT8aGhYFF0QCVG1qsI5BIGrSB7D5WtPTJRK8U9EKqpr7cnGjTIoAchiKWUkbFo8c" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const servers = @json($servers);
    const specUrl = @json($specUrl);

    const currentEnvElement = document.getElementById('current-env');
    const dropdown = document.getElementById('dropdown');
    const envOptionButtons = document.querySelectorAll('.environment-option');
    const selectServerButtons = document.querySelectorAll('.select-server-button');
    const pingServerButtons = document.querySelectorAll('.ping-server-button');
    const serverCards = document.querySelectorAll('.server-card');
    const copyApiUrlButton = document.getElementById('copy-api-url');
    const copyButtonText = document.getElementById('copy-button-text');
    const healthInput = document.getElementById('health-check-url');
    const healthButton = document.getElementById('test-health-button');
    const healthResult = document.getElementById('health-result');
    const envChecks = document.querySelectorAll('#env-check-1, #env-check-2, #env-check-3');
    const envStatusBadge = document.getElementById('env-status-badge');
    const authButton = document.getElementById('auth-button');
    const bearerInput = document.getElementById('bearer-token');
    const applyAuthButton = document.getElementById('apply-auth-button');
    const testLoginButton = document.getElementById('test-login-button');

    let activeServerKey = null;

    if (window.AOS) {
        window.AOS.init({ duration: 800, easing: 'ease-in-out', once: true, mirror: false });
    }

    if (window.Motion) {
        const { animate, stagger } = window.Motion;
        animate('.hero-title', { opacity: [0, 1], transform: ['translateY(24px)', 'translateY(0)'] }, { duration: 0.7, delay: 0.15, easing: 'ease-out' });
        animate('.hero-subtitle', { opacity: [0, 1], transform: ['translateY(18px)', 'translateY(0)'] }, { duration: 0.7, delay: 0.22, easing: 'ease-out' });
        animate('.hero-button', { opacity: [0, 1], transform: ['translateY(14px)', 'translateY(0)'] }, { duration: 0.55, delay: stagger(0.12, { start: 0.3 }), easing: 'ease-out' });
        animate('.feature-card', { opacity: [0, 1], transform: ['translateY(18px)', 'translateY(0)'] }, { duration: 0.6, delay: stagger(0.08, { start: 0.4 }), easing: 'ease-out' });
    }

    if (window.gsap) {
        window.gsap.to('.floating-orb', { duration: 6, y: -18, repeat: -1, yoyo: true, ease: 'sine.inOut', stagger: 0.6 });
    }

    if (window.ClipboardJS) {
        new ClipboardJS('.copy-code-button', {
            target: trigger => document.querySelector(trigger.getAttribute('data-clipboard-target'))
        }).on('success', event => {
            const original = event.trigger.innerHTML;
            event.trigger.innerHTML = '<svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            setTimeout(() => {
                event.trigger.innerHTML = original;
            }, 2000);
            event.clearSelection();
        });
    }

    const swagger = SwaggerUIBundle({
        url: specUrl,
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIBundle.SwaggerUIStandalonePreset
        ],
        layout: 'BaseLayout',
        docExpansion: 'none',
        defaultModelsExpandDepth: 0,
        syntaxHighlight: {
            activated: true,
            theme: 'obsidian'
        },
        requestInterceptor: req => {
            const storedToken = localStorage.getItem('gerobaks_bearer_token');
            if (storedToken && !req.headers.Authorization) {
                req.headers.Authorization = `Bearer ${storedToken}`;
            }
            return req;
        }
    });
    window.ui = swagger;

    function getServerByKey(key) {
        return servers.find(server => server.key === key);
    }

    function updateEnvStatusBadge() {
        if (!envStatusBadge) return;
        const total = envChecks.length;
        const checked = Array.from(envChecks).filter(item => item.checked).length;
        envStatusBadge.className = 'text-xs font-medium px-2.5 py-0.5 rounded';
        if (checked === 0) {
            envStatusBadge.classList.add('bg-amber-900/50', 'text-amber-300');
            envStatusBadge.textContent = 'Pending';
        } else if (checked < total) {
            envStatusBadge.classList.add('bg-blue-900/50', 'text-blue-300');
            envStatusBadge.textContent = 'In Progress';
        } else {
            envStatusBadge.classList.add('bg-emerald-900/50', 'text-emerald-300');
            envStatusBadge.textContent = 'Complete';
        }
    }

    envChecks.forEach(check => check.addEventListener('change', updateEnvStatusBadge));
    updateEnvStatusBadge();

    function decorateStatusBadge(badge, variant) {
        if (!badge) return;
        const baseClass = 'server-status-badge inline-flex items-center gap-1 text-[11px] font-medium px-2 py-1 rounded-full';
        switch (variant) {
            case 'active':
                badge.className = `${baseClass} bg-primary-900/40 text-primary-200 border border-primary-500/40`;
                badge.textContent = 'Active';
                break;
            case 'healthy':
                badge.className = `${baseClass} bg-emerald-900/40 text-emerald-300 border border-emerald-500/40`;
                badge.textContent = 'Healthy';
                break;
            case 'offline':
                badge.className = `${baseClass} bg-rose-900/30 text-rose-200 border border-rose-600/40`;
                badge.textContent = 'Offline';
                break;
            case 'checking':
                badge.className = `${baseClass} bg-sky-900/30 text-sky-200 border border-sky-500/40`;
                badge.textContent = 'Checking…';
                break;
            default:
                badge.className = `${baseClass} bg-slate-800 text-slate-300`;
                badge.textContent = 'Idle';
        }
    }

    function setActiveServer(key) {
        const server = getServerByKey(key) || servers[0];
        if (!server) return;
        activeServerKey = server.key;

        if (currentEnvElement) {
            currentEnvElement.textContent = server.label;
            currentEnvElement.dataset.url = server.url;
            currentEnvElement.dataset.docs = server.docs || '';
        }

        if (healthInput) {
            healthInput.value = server.url.replace(/\/$/, '') + '/api/health';
        }

        envOptionButtons.forEach(button => {
            const isActive = button.dataset.envKey === server.key;
            button.classList.toggle('bg-slate-700/70', isActive);
            button.classList.toggle('border', isActive);
            button.classList.toggle('border-primary-500/40', isActive);
        });

        serverCards.forEach(card => {
            const isActive = card.dataset.envKey === server.key;
            card.classList.toggle('ring-2', isActive);
            card.classList.toggle('ring-primary-500', isActive);
            card.classList.toggle('shadow-lg', isActive);
            card.classList.toggle('shadow-primary-900/40', isActive);
            const badge = card.querySelector('.server-status-badge');
            if (badge) {
                if (isActive && badge.textContent === 'Idle') {
                    decorateStatusBadge(badge, 'active');
                } else if (!isActive && badge.textContent === 'Active') {
                    decorateStatusBadge(badge, 'idle');
                }
            }
            if (!isActive) {
                const pingResult = card.querySelector('.ping-result');
                if (pingResult && !pingResult.dataset.persist) {
                    pingResult.classList.add('hidden');
                }
            }
        });

        if (window.ui && typeof window.ui.setServers === 'function') {
            try {
                window.ui.setServers([{ url: server.url }]);
            } catch (error) {
                console.warn('Unable to update Swagger servers', error);
            }
        }

        if (window.gsap) {
            const activeCard = document.querySelector(`.server-card[data-env-key="${server.key}"]`);
            if (activeCard) {
                window.gsap.fromTo(activeCard, { scale: 0.97 }, { scale: 1, duration: 0.4, ease: 'power2.out' });
            }
        }
    }

    envOptionButtons.forEach(button => {
        button.addEventListener('click', () => {
            setActiveServer(button.dataset.envKey);
            if (dropdown) dropdown.classList.add('hidden');
        });
    });

    selectServerButtons.forEach(button => {
        button.addEventListener('click', () => setActiveServer(button.dataset.envKey));
    });

    function pingServer(key) {
        const server = getServerByKey(key);
        const card = document.querySelector(`.server-card[data-env-key="${key}"]`);
        if (!server || !card) return;
        const badge = card.querySelector('.server-status-badge');
        const result = card.querySelector('.ping-result');
        const healthUrl = server.url.replace(/\/$/, '') + '/api/health';

        if (badge) {
            decorateStatusBadge(badge, 'checking');
        }

        if (result) {
            result.dataset.persist = 'true';
            result.className = 'ping-result mt-3 text-[11px] px-2 py-1 rounded border border-slate-700 bg-slate-800 text-slate-200 flex items-center gap-2';
            result.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Checking availability…</span>';
            result.classList.remove('hidden');
        }

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 8000);
        const started = performance.now();

        fetch(healthUrl, { signal: controller.signal })
            .then(response => {
                clearTimeout(timeout);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json().catch(() => ({}));
            })
            .then(() => {
                const latency = Math.round(performance.now() - started);
                if (result) {
                    result.className = 'ping-result mt-3 text-[11px] px-2 py-1 rounded border border-emerald-500/30 bg-emerald-900/30 text-emerald-200';
                    result.innerHTML = `<strong>Healthy</strong> • ${latency}ms`;
                }
                if (badge) {
                    decorateStatusBadge(badge, 'healthy');
                }
                if (window.gsap) {
                    window.gsap.fromTo(card, { boxShadow: '0 0 0 rgba(16, 185, 129, 0)' }, { boxShadow: '0 0 24px rgba(16, 185, 129, 0.35)', duration: 0.6, ease: 'power2.out', yoyo: true, repeat: 1 });
                }
            })
            .catch(error => {
                clearTimeout(timeout);
                if (result) {
                    const message = error.name === 'AbortError' ? 'Timeout' : (error.message || 'Unknown error');
                    result.className = 'ping-result mt-3 text-[11px] px-2 py-1 rounded border border-rose-500/30 bg-rose-900/30 text-rose-200';
                    result.innerHTML = `<strong>Unavailable</strong> • ${message}`;
                }
                if (badge) {
                    decorateStatusBadge(badge, 'offline');
                }
            });
    }

    pingServerButtons.forEach(button => {
        button.addEventListener('click', () => pingServer(button.dataset.envKey));
    });

    if (copyApiUrlButton) {
        copyApiUrlButton.addEventListener('click', event => {
            event.preventDefault();
            const targetUrl = currentEnvElement?.dataset.url || (servers[0] && servers[0].url);
            if (!targetUrl) return;
            navigator.clipboard.writeText(targetUrl).then(() => {
                if (copyButtonText) {
                    const original = copyButtonText.textContent;
                    copyButtonText.textContent = 'Copied!';
                    setTimeout(() => {
                        copyButtonText.textContent = original;
                    }, 2000);
                }
                if (window.gsap) {
                    window.gsap.fromTo(copyApiUrlButton, { scale: 0.94 }, { scale: 1, duration: 0.3, ease: 'power2.out' });
                }
            });
        });
    }

    if (healthButton && healthInput && healthResult) {
        healthButton.addEventListener('click', () => {
            const url = healthInput.value.trim();
            if (!url) return;
            healthResult.className = 'mt-2 text-xs p-2 rounded bg-slate-800 flex items-center gap-2';
            healthResult.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Testing…</span>';
            healthResult.classList.remove('hidden');

            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 8000);
            const start = performance.now();

            fetch(url, { signal: controller.signal })
                .then(response => {
                    clearTimeout(timeout);
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json().catch(() => ({}));
                })
                .then(() => {
                    const latency = Math.round(performance.now() - start);
                    healthResult.className = 'mt-2 text-xs p-2 rounded bg-emerald-900/30 text-emerald-200 flex items-center gap-2';
                    healthResult.innerHTML = '<svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>API is healthy • ' + latency + 'ms</span>';
                })
                .catch(error => {
                    clearTimeout(timeout);
                    const message = error.name === 'AbortError' ? 'Request timed out' : (error.message || 'Unknown error');
                    healthResult.className = 'mt-2 text-xs p-2 rounded bg-rose-900/30 text-rose-200 flex items-center gap-2';
                    healthResult.innerHTML = '<svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg><span>' + message + '</span>';
                });
        });
    }

    const ModalClass = window.Modal || (window.flowbite && window.flowbite.Modal) || (window.Flowbite && window.Flowbite.Modal);
    const authModalElement = document.getElementById('auth-modal');
    const authModalInstance = ModalClass && authModalElement ? new ModalClass(authModalElement, { closable: true }) : null;

    function applyAuthorizedState() {
        if (!authButton) return;
        authButton.innerHTML = '<svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Authorized';
        authButton.classList.remove('bg-primary-500', 'border-primary-600', 'text-slate-900');
        authButton.classList.add('bg-emerald-500', 'border-emerald-600', 'text-white');
    }

    authButton?.addEventListener('click', () => {
        if (authModalInstance?.show) {
            authModalInstance.show();
        } else if (authModalElement) {
            authModalElement.classList.remove('hidden');
        }
    });

    applyAuthButton?.addEventListener('click', () => {
        const token = bearerInput?.value.trim();
        if (!token) return;
        localStorage.setItem('gerobaks_bearer_token', token);
        applyAuthorizedState();
        if (authModalInstance?.hide) {
            authModalInstance.hide();
        }
        if (window.gsap) {
            window.gsap.fromTo(authButton, { scale: 0.93 }, { scale: 1, duration: 0.25, ease: 'power2.out' });
        }
    });

    testLoginButton?.addEventListener('click', () => {
        testLoginButton.disabled = true;
        testLoginButton.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generating…';

        setTimeout(() => {
            const mockToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6Ikdlcm9iYWtzIERlbW8iLCJpYXQiOjE2OTQ0NDE2ODB9.eHnCJ0LypZDLzBIZA1U_mPCJiwrCD0hYbXRIn9zebTA';
            if (bearerInput) {
                bearerInput.value = mockToken;
            }
            localStorage.setItem('gerobaks_bearer_token', mockToken);
            applyAuthorizedState();
            testLoginButton.disabled = false;
            testLoginButton.innerHTML = 'Generate Token';
        }, 1500);
    });

    const storedToken = localStorage.getItem('gerobaks_bearer_token');
    if (storedToken && bearerInput) {
        bearerInput.value = storedToken;
        applyAuthorizedState();
    }

    const versionFilterButtons = document.querySelectorAll('.version-filter-btn');
    versionFilterButtons.forEach(button => {
        button.addEventListener('click', () => {
            versionFilterButtons.forEach(btn => {
                btn.classList.remove('bg-violet-900/20', 'border-violet-600', 'text-violet-300');
                btn.classList.add('bg-slate-800', 'border-slate-700', 'text-slate-300');
            });
            button.classList.remove('bg-slate-800', 'border-slate-700', 'text-slate-300');
            button.classList.add('bg-violet-900/20', 'border-violet-600', 'text-violet-300');

            const version = button.getAttribute('data-version');
            const headings = document.querySelectorAll('.changelog h2');
            headings.forEach(heading => {
                const container = heading.parentElement;
                if (!container) return;
                if (version === 'all' || heading.textContent.toLowerCase().includes(version)) {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });
        });
    });

    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    function syncTheme() {
        const prefersDark = localStorage.getItem('color-theme') === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (prefersDark) {
            document.documentElement.classList.add('dark');
            themeToggleLightIcon?.classList.remove('hidden');
            themeToggleDarkIcon?.classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            themeToggleDarkIcon?.classList.remove('hidden');
            themeToggleLightIcon?.classList.add('hidden');
        }
    }

    themeToggleBtn?.addEventListener('click', () => {
        const current = localStorage.getItem('color-theme');
        if (current) {
            localStorage.setItem('color-theme', current === 'light' ? 'dark' : 'light');
        } else {
            localStorage.setItem('color-theme', document.documentElement.classList.contains('dark') ? 'light' : 'dark');
        }
        syncTheme();
    });
    syncTheme();

    if (servers.length) {
        setActiveServer(servers[0].key);
    }
});
</script>
</body>
</html>
