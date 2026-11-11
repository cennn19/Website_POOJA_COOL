<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Administrator - CV POOJA COOL</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 text-neutral-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full space-y-6">
        <!-- Logo and Brand -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-primary-600 text-white font-bold text-lg flex items-center justify-center mx-auto shadow-md">
                SA
            </div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Login Administrator</h1>
            <p class="text-xs text-neutral-500">Masuk ke panel pengelola pesanan SejukAbadi HVAC</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-neutral-200 shadow-sm space-y-6">
            @if ($errors->any())
                <x-alert type="danger">
                    {{ $errors->first() }}
                </x-alert>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
                @csrf

                <x-input
                    label="Email Administrator"
                    name="email"
                    type="email"
                    placeholder="admin@sejukabadi.com"
                    required
                    autofocus
                />

                <x-input
                    label="Kata Sandi"
                    name="password"
                    type="password"
                    placeholder="••••••••"
                    required
                />

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-neutral-700 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-neutral-300 text-primary-600 focus:ring-primary-500"
                        />
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 rounded-xl text-base font-bold text-white bg-primary-600 hover:bg-primary-700 active:bg-primary-800 transition duration-150 flex items-center justify-center gap-2 cursor-pointer shadow-md min-h-[44px]"
                    >
                        <span>Masuk ke Dashboard</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center text-xs text-neutral-500">
            <a href="{{ route('home') }}" class="hover:text-primary-600 transition underline">
                &larr; Kembali ke Website Publik
            </a>
        </div>
    </div>

</body>
</html>
