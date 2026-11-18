<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Admin Panel') | POOJA COOL</title>

    <!-- Google Fonts: Inter Tight & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-mist text-ink font-sans antialiased min-h-screen selection:bg-blue selection:text-white" x-data="{ menuOpen: false }">

    @php
        $adminMenu = [
            ['label' => 'Daftar Pesanan', 'route' => 'admin.orders.index', 'active' => 'admin.orders.*', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['label' => 'Layanan & Harga', 'route' => 'admin.services.index', 'active' => 'admin.services.*', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
            ['label' => 'Cek Keamanan', 'route' => 'admin.security.index', 'active' => 'admin.security.*', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['label' => 'Ganti Password', 'route' => 'admin.password.edit', 'active' => 'admin.password.*', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
        ];
    @endphp

    <div class="lg:flex min-h-screen">
        <!-- Sidebar (desktop) -->
        <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-paper border-r border-line sticky top-0 h-screen">
            <div class="h-16 flex items-center gap-2 px-6 border-b border-line">
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2">
                    <span class="font-heading font-semibold text-lg tracking-tight text-ink">POOJA COOL</span>
                    <span class="text-[10px] uppercase tracking-wider text-ink-mute font-medium px-2 py-0.5 rounded-full bg-mist">Admin</span>
                </a>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-1" aria-label="Menu admin">
                <span class="block px-3 pb-2 text-[11px] uppercase tracking-[0.12em] text-ink-mute font-medium">Menu</span>
                @foreach ($adminMenu as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition duration-150 {{ request()->routeIs($item['active']) ? 'bg-ink text-paper font-medium' : 'text-ink-soft hover:bg-mist hover:text-ink' }}"
                        @if (request()->routeIs($item['active'])) aria-current="page" @endif
                    >
                        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <a
                    href="{{ route('home') }}"
                    target="_blank"
                    rel="noopener"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-ink-soft hover:bg-mist hover:text-ink transition duration-150"
                >
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Lihat Website
                </a>
            </nav>

            <div class="border-t border-line p-4 space-y-3">
                <div class="px-2">
                    <span class="block text-xs font-medium text-ink leading-tight truncate">{{ auth()->user()->name ?? 'Administrator' }}</span>
                    <span class="block text-[11px] text-ink-mute leading-tight truncate">{{ auth()->user()->email ?? '' }}</span>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 rounded-full border border-line text-xs uppercase tracking-wider font-semibold text-ink hover:bg-mist transition duration-150 cursor-pointer min-h-[44px]"
                    >
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 min-w-0 flex flex-col min-h-screen">
            <!-- Top bar (mobile & tablet) -->
            <header class="lg:hidden bg-paper border-b border-line sticky top-0 z-40">
                <div class="px-4 md:px-8 h-16 flex items-center justify-between">
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2">
                        <span class="font-heading font-semibold text-lg tracking-tight text-ink">POOJA COOL</span>
                        <span class="text-[10px] uppercase tracking-wider text-ink-mute font-medium px-2 py-0.5 rounded-full bg-mist">Admin</span>
                    </a>

                    <button
                        type="button"
                        @click="menuOpen = !menuOpen"
                        class="p-2.5 rounded-xl text-ink hover:bg-mist transition cursor-pointer"
                        :aria-expanded="menuOpen.toString()"
                        aria-label="Buka menu admin"
                    >
                        <svg x-show="!menuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="menuOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div
                    x-show="menuOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="border-t border-line px-4 md:px-8 pt-3 pb-5 space-y-1"
                >
                    @foreach ($adminMenu as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="block px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs($item['active']) ? 'bg-mist text-ink font-medium' : 'text-ink-soft hover:bg-mist' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="block px-3 py-2.5 rounded-xl text-sm text-ink-soft hover:bg-mist">
                        Lihat Website ↗
                    </a>

                    <div class="pt-3 mt-2 border-t border-line flex items-center justify-between gap-4">
                        <span class="text-xs text-ink-soft truncate">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-full border border-line text-xs uppercase tracking-wider font-semibold text-ink hover:bg-mist transition cursor-pointer min-h-[44px]"
                            >
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Global Notifications -->
            <div class="max-w-[1200px] mx-auto px-4 md:px-8 w-full mt-6">
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
            </div>

            <!-- Main Content -->
            <main class="flex-grow max-w-[1200px] mx-auto px-4 md:px-8 py-6 lg:py-8 w-full">
                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <footer class="border-t border-line py-4">
                <div class="max-w-[1200px] mx-auto px-4 md:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-ink-mute gap-2">
                    <span>&copy; {{ date('Y') }} POOJA COOL &bull; Panel Administrasi</span>
                    <span>{{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
            </footer>
        </div>
    </div>

</body>
</html>
