<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'SejukAbadi HVAC') }} - Jasa Service & Instalasi AC Profesional</title>
    <meta name="description" content="{{ $metaDescription ?? 'Jasa service AC, cuci AC hemat, isi freon, bongkar pasang, dan perawatan HVACR bergaransi resmi untuk rumah dan kantor di Jabodetabek.' }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 text-neutral-900 font-sans antialiased min-h-screen flex flex-col selection:bg-primary-500 selection:text-white">

    <!-- Top Announcement Bar -->
    <div class="bg-primary-900 text-primary-100 text-xs py-2 px-4 text-center font-medium">
        <span>Teknisi Bergaransi Resmi 30 Hari &bull; Layanan Cepat Area Jabodetabek &bull; Siap Datang Hari Ini</span>
    </div>

    <!-- Sticky Navigation -->
    <header
        x-data="{ mobileOpen: false, scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 10)"
        :class="{ 'shadow-md bg-white/95 backdrop-blur-md': scrolled, 'bg-white shadow-sm': !scrolled }"
        class="sticky top-0 z-50 transition duration-200 border-b border-neutral-200/60"
    >
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-primary-900 block leading-tight">Sejuk<span class="text-primary-600">Abadi</span></span>
                        <span class="text-[11px] font-semibold text-neutral-600 uppercase tracking-widest block -mt-0.5">HVAC & AC Service</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-8">
                    <a
                        href="{{ route('home') }}"
                        class="text-sm font-medium transition duration-150 py-1 border-b-2 {{ request()->routeIs('home') ? 'text-primary-600 border-primary-600 font-semibold' : 'text-neutral-600 hover:text-neutral-900 border-transparent' }}"
                    >
                        Home
                    </a>
                    <a
                        href="{{ route('order.create') }}"
                        class="text-sm font-medium transition duration-150 py-1 border-b-2 {{ request()->routeIs('order.create') ? 'text-primary-600 border-primary-600 font-semibold' : 'text-neutral-600 hover:text-neutral-900 border-transparent' }}"
                    >
                        Order Layanan
                    </a>
                    <a
                        href="{{ route('about') }}"
                        class="text-sm font-medium transition duration-150 py-1 border-b-2 {{ request()->routeIs('about') ? 'text-primary-600 border-primary-600 font-semibold' : 'text-neutral-600 hover:text-neutral-900 border-transparent' }}"
                    >
                        Tentang Kami
                    </a>
                    <a
                        href="{{ route('contact') }}"
                        class="text-sm font-medium transition duration-150 py-1 border-b-2 {{ request()->routeIs('contact') ? 'text-primary-600 border-primary-600 font-semibold' : 'text-neutral-600 hover:text-neutral-900 border-transparent' }}"
                    >
                        Kontak
                    </a>
                    <a
                        href="{{ route('order.status') }}"
                        class="text-sm font-medium transition duration-150 py-1 border-b-2 {{ request()->routeIs('order.status') ? 'text-primary-600 border-primary-600 font-semibold' : 'text-neutral-600 hover:text-neutral-900 border-transparent' }}"
                    >
                        Cek Status
                    </a>
                </nav>

                <!-- Desktop Action Button -->
                <div class="hidden md:flex items-center gap-3">
                    <x-button href="{{ route('order.create') }}" variant="accent" size="md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Pesan Sekarang
                    </x-button>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex md:hidden items-center">
                    <button
                        type="button"
                        @click="mobileOpen = !mobileOpen"
                        class="p-2.5 rounded-xl text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        aria-label="Toggle menu"
                    >
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div
            x-show="mobileOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden bg-white border-b border-neutral-200 px-4 pt-2 pb-6 space-y-2 shadow-lg"
        >
            <a
                href="{{ route('home') }}"
                class="block px-3 py-2.5 rounded-xl text-base font-medium {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-neutral-700 hover:bg-neutral-50' }}"
            >
                Home
            </a>
            <a
                href="{{ route('order.create') }}"
                class="block px-3 py-2.5 rounded-xl text-base font-medium {{ request()->routeIs('order.create') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-neutral-700 hover:bg-neutral-50' }}"
            >
                Order Layanan
            </a>
            <a
                href="{{ route('about') }}"
                class="block px-3 py-2.5 rounded-xl text-base font-medium {{ request()->routeIs('about') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-neutral-700 hover:bg-neutral-50' }}"
            >
                Tentang Kami
            </a>
            <a
                href="{{ route('contact') }}"
                class="block px-3 py-2.5 rounded-xl text-base font-medium {{ request()->routeIs('contact') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-neutral-700 hover:bg-neutral-50' }}"
            >
                Kontak
            </a>
            <a
                href="{{ route('order.status') }}"
                class="block px-3 py-2.5 rounded-xl text-base font-medium {{ request()->routeIs('order.status') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-neutral-700 hover:bg-neutral-50' }}"
            >
                Cek Status Order
            </a>

            <div class="pt-3">
                <x-button href="{{ route('order.create') }}" variant="accent" class="w-full">
                    Pesan Sekarang
                </x-button>
            </div>
        </div>
    </header>

    <!-- Global Flash Notification Container -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 w-full mt-4">
        @if (session('success'))
            <x-alert type="success" class="mb-4">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="danger" class="mb-4">
                {{ session('error') }}
            </x-alert>
        @endif

        @if (session('info'))
            <x-alert type="info" class="mb-4">
                {{ session('info') }}
            </x-alert>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-primary-900 text-white mt-20 pt-16 pb-12 border-t border-primary-700/50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-primary-700/40">
                <!-- Column 1: Info Brand -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-primary-600 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">Sejuk<span class="text-primary-500">Abadi</span></span>
                    </div>
                    <p class="text-neutral-300 text-sm leading-relaxed">
                        Penyedia jasa instalasi, perawatan, cuci AC, dan perbaikan sistem HVACR terpercaya. Teknisi bersertifikat, harga transparan, dan bergaransi 30 hari.
                    </p>
                    <div class="pt-1">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-800/80 text-primary-100 text-xs font-medium border border-primary-700">
                            <span class="w-2 h-2 rounded-full bg-success-600 animate-pulse"></span>
                            Buka Setiap Hari: 08.00 - 20.00 WIB
                        </span>
                    </div>
                </div>

                <!-- Column 2: Tautan Menu -->
                <div>
                    <h3 class="text-base font-semibold text-white tracking-wide mb-4">Navigasi Utama</h3>
                    <ul class="space-y-2.5 text-sm text-neutral-300">
                        <li><a href="{{ route('home') }}" class="hover:text-primary-500 transition">Beranda</a></li>
                        <li><a href="{{ route('order.create') }}" class="hover:text-primary-500 transition">Order Layanan</a></li>
                        <li><a href="{{ route('order.status') }}" class="hover:text-primary-500 transition">Cek Status Pesanan</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-primary-500 transition">Tentang Kami</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-primary-500 transition">Kontak & Lokasi</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-primary-500 transition text-neutral-400">Login Administrator</a></li>
                    </ul>
                </div>

                <!-- Column 3: Area Layanan -->
                <div>
                    <h3 class="text-base font-semibold text-white tracking-wide mb-4">Area Jangkauan</h3>
                    <p class="text-sm text-neutral-300 mb-3">Melayani seluruh wilayah Jabodetabek tanpa biaya tambahan transport zonasi terjangkau:</p>
                    <ul class="space-y-1.5 text-sm text-neutral-300">
                        <li class="flex items-center gap-2">&bull; Jakarta Selatan, Timur, Barat, Pusat, Utara</li>
                        <li class="flex items-center gap-2">&bull; Kota & Kabupaten Bogor</li>
                        <li class="flex items-center gap-2">&bull; Kota Depok & sekitarnya</li>
                        <li class="flex items-center gap-2">&bull; Tangerang Kota, Tangsel & BSD</li>
                        <li class="flex items-center gap-2">&bull; Bekasi Kota & Kabupaten</li>
                    </ul>
                </div>

                <!-- Column 4: Kontak Cepat -->
                <div>
                    <h3 class="text-base font-semibold text-white tracking-wide mb-4">Hubungi Kami</h3>
                    <ul class="space-y-3 text-sm text-neutral-300">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-primary-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Jl. Raya Pendingin Sejuk No. 88, Kebayoran Baru, Jakarta Selatan</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-primary-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="hover:text-primary-500 transition font-medium">0812-3456-7890 (WhatsApp)</a>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-primary-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>layanan@sejukabadi.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-neutral-400">
                <p>&copy; {{ date('Y') }} SejukAbadi HVAC. Seluruh hak cipta dilindungi undang-undang.</p>
                <p>Website Resmi Pelayanan Jasa HVACR & Servis AC Rumahan</p>
            </div>
        </div>
    </footer>

</body>
</html>
