<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Panel' }} - SejukAbadi HVAC</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 text-neutral-900 font-sans antialiased min-h-screen flex flex-col">

    <!-- Admin Top Navbar -->
    <header class="bg-white border-b border-neutral-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center text-white font-bold text-sm">
                            SA
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold tracking-tight text-neutral-900">SejukAbadi</span>
                            <span class="text-[10px] text-neutral-500 font-medium -mt-0.5">Admin Dashboard</span>
                        </div>
                    </a>

                    <!-- Nav items -->
                    <nav class="hidden md:flex items-center gap-2">
                        <a
                            href="{{ route('admin.orders.index') }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100' }}"
                        >
                            Daftar Pesanan
                        </a>
                        <a
                            href="{{ route('home') }}"
                            target="_blank"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 flex items-center gap-1 transition"
                        >
                            <span>Lihat Website</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </nav>
                </div>

                <!-- Admin User Info & Logout -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-sm font-semibold text-neutral-900 leading-tight">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <span class="text-xs text-neutral-500 leading-tight">{{ auth()->user()->email ?? 'admin@sejukabadi.com' }}</span>
                    </div>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="px-3.5 py-1.5 rounded-lg border border-neutral-200 text-xs font-semibold text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition flex items-center gap-1.5 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Global Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
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
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-neutral-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-neutral-500 gap-2">
            <span>&copy; {{ date('Y') }} SejukAbadi HVAC - Panel Kelola Pesanan</span>
            <span>Waktu Server: {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
        </div>
    </footer>

</body>
</html>
