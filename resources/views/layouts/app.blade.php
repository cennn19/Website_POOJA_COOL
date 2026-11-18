<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F2A44">

    @php
        $pageTitle = html_entity_decode(trim($__env->yieldContent('title')), ENT_QUOTES);
        $pageTitle = $pageTitle !== '' ? $pageTitle.' | '.config('usaha.nama') : config('usaha.nama').' - Servis, Cuci & Instalasi AC di '.config('usaha.lokasi');
        $pageDescription = html_entity_decode(trim($__env->yieldContent('description')), ENT_QUOTES) ?: 'Servis, cuci, instalasi, dan perbaikan AC rumahan di '.config('usaha.lokasi').'. Pesan online, harga jelas, bayar via QRIS, transfer, atau e-wallet.';
        $ogImage = trim($__env->yieldContent('og_image')) ?: (file_exists(public_path('images/og.jpg')) ? asset('images/og.jpg') : null);
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @stack('meta')

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="{{ config('usaha.nama') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    <!-- Google Fonts: Inter Tight (heading) & Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper text-ink font-sans antialiased min-h-screen flex flex-col selection:bg-blue selection:text-white">

    @php
        $navItems = [
            ['label' => 'home', 'mobile' => 'Home', 'route' => 'home'],
            ['label' => 'order', 'mobile' => 'Order Layanan', 'route' => 'order.create'],
            ['label' => 'tentang kami', 'mobile' => 'Tentang Kami', 'route' => 'about'],
            ['label' => 'kontak', 'mobile' => 'Kontak', 'route' => 'contact'],
            ['label' => 'cek status', 'mobile' => 'Cek Status Order', 'route' => 'order.status'],
        ];
    @endphp

    <!-- Navbar -->
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-paper border-b border-line text-ink">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Wordmark -->
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="font-heading font-bold text-xl tracking-tight text-ink">{{ config('usaha.nama') }}</span>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden lg:flex items-center gap-8 text-[13px] font-medium" aria-label="Menu utama">
                    @foreach ($navItems as $item)
                        @php($active = request()->routeIs($item['route']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="py-1 transition {{ $active ? 'text-ink font-semibold underline underline-offset-8 decoration-2 decoration-blue' : 'text-ink-mute hover:text-ink' }}"
                            @if ($active) aria-current="page" @endif
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <!-- Desktop CTA -->
                <div class="hidden lg:flex items-center">
                    <x-button href="{{ route('order.create') }}" variant="primary" size="md">
                        PESAN SEKARANG
                    </x-button>
                </div>

                <!-- Mobile Hamburger -->
                <div class="flex lg:hidden items-center">
                    <button
                        type="button"
                        @click="mobileOpen = !mobileOpen"
                        class="p-2 rounded-xl text-ink hover:bg-ice transition cursor-pointer"
                        :aria-expanded="mobileOpen.toString()"
                        aria-label="Buka menu"
                    >
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div
            x-show="mobileOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden bg-paper text-ink border-t border-line px-4 md:px-8 pt-4 pb-8 space-y-4"
        >
            <nav class="space-y-1" aria-label="Menu utama seluler">
                @foreach ($navItems as $item)
                    @php($active = request()->routeIs($item['route']))
                    <a
                        href="{{ route($item['route']) }}"
                        class="block px-3 py-2.5 rounded-xl text-base font-medium {{ $active ? 'bg-ice text-ink font-semibold' : 'text-ink hover:bg-ice' }}"
                    >
                        {{ $item['mobile'] }}
                    </a>
                @endforeach
            </nav>

            <x-button href="{{ route('order.create') }}" variant="primary" class="w-full">
                PESAN SEKARANG
            </x-button>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if (session('success') || session('error') || session('info'))
        <div class="max-w-[1440px] mx-auto px-4 md:px-8 w-full mt-4">
            @if (session('success'))
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="danger" class="mb-4">{{ session('error') }}</x-alert>
            @endif
            @if (session('info'))
                <x-alert type="info" class="mb-4">{{ session('info') }}</x-alert>
            @endif
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-white rounded-t-[28px] mt-24 md:mt-32 pt-16 md:pt-24 pb-8 relative overflow-hidden">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-16 border-b border-white/20">
                <!-- Brand -->
                <div class="space-y-4">
                    <span class="font-heading font-bold text-2xl tracking-tight block">{{ config('usaha.nama') }}</span>
                    <p class="text-ice text-sm leading-relaxed max-w-sm">
                        Servis, cuci, instalasi, dan perbaikan AC rumahan. Pesan online, harga jelas di awal, bayar via QRIS, transfer bank, atau e-wallet.
                    </p>
                    <p class="text-sm text-white">
                        <span class="block text-ice text-xs uppercase tracking-wider mb-0.5">Jam Operasional</span>
                        {{ config('usaha.jam_operasional') }}
                    </p>
                </div>

                <!-- Perusahaan -->
                <div class="space-y-4">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.12em] text-ice">Perusahaan</h2>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="text-white hover:underline underline-offset-4">Beranda</a></li>
                        <li><a href="{{ route('order.create') }}" class="text-white hover:underline underline-offset-4">Order Layanan</a></li>
                        <li><a href="{{ route('about') }}" class="text-white hover:underline underline-offset-4">Tentang Kami</a></li>
                        <li><a href="{{ route('contact') }}" class="text-white hover:underline underline-offset-4">Kontak</a></li>
                        <li><a href="{{ route('order.status') }}" class="text-white hover:underline underline-offset-4">Cek Status Pesanan</a></li>
                    </ul>
                </div>

                <!-- Layanan -->
                <div class="space-y-4">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.12em] text-ice">Layanan</h2>
                    <ul class="space-y-2.5 text-sm">
                        @foreach (['Cleaning AC', 'Pasang AC', 'Instalasi Listrik', 'Tambah Freon', 'Service dan Perbaikan AC'] as $namaLayanan)
                            <li><a href="{{ route('home') }}#layanan" class="text-white hover:underline underline-offset-4">{{ $namaLayanan }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Kontak -->
                <div class="space-y-4">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.12em] text-ice">Kontak</h2>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <span class="block text-ice text-xs uppercase tracking-wider mb-0.5">Telepon</span>
                            <span class="select-text">{{ config('usaha.telepon') }}</span>
                        </li>
                        <li>
                            <span class="block text-ice text-xs uppercase tracking-wider mb-0.5">WhatsApp</span>
                            <a href="{{ \App\Support\WhatsApp::adminLink() }}" target="_blank" rel="noopener noreferrer" class="font-medium hover:underline underline-offset-4">{{ \App\Support\WhatsApp::display(\App\Support\WhatsApp::adminNumber()) }}</a>
                        </li>
                        <li>
                            <span class="block text-ice text-xs uppercase tracking-wider mb-0.5">Email</span>
                            <span class="select-text break-all">{{ config('usaha.email') }}</span>
                        </li>
                        <li>
                            <span class="block text-ice text-xs uppercase tracking-wider mb-0.5">Alamat</span>
                            <span class="select-text leading-relaxed">{{ config('usaha.alamat') }}</span>
                        </li>
                        <li>
                            <span class="block text-ice text-xs uppercase tracking-wider mb-0.5">Area Layanan</span>
                            <span>{{ config('usaha.area_layanan') }}</span>
                        </li>
                        <li class="pt-1">
                            <a href="{{ route('admin.login') }}" class="text-xs text-ice hover:text-white transition">&rarr; Login Administrator</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-6 pb-2 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-ice">
                <p>&copy; {{ date('Y') }} {{ config('usaha.nama_legal') }}. Seluruh hak cipta dilindungi.</p>
                <p>Jasa servis &amp; instalasi AC rumahan</p>
            </div>
        </div>
    </footer>

</body>
</html>
