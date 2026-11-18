<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Administrator - POOJA COOL</title>

    <!-- Google Fonts: Inter Tight & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-mist text-ink font-sans antialiased min-h-screen flex items-center justify-center p-4 selection:bg-blue selection:text-white">

    <div class="max-w-md w-full space-y-6">
        <!-- Logo and Brand -->
        <div class="text-center space-y-1">
            <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">PANEL PENGELOLA</span>
            <h1 class="text-2xl sm:text-3xl font-heading font-medium text-ink tracking-tight">
                POOJA COOL
            </h1>
            <p class="text-xs text-ink-mute">Masuk untuk mengelola pesanan & verifikasi pembayaran</p>
        </div>

        <!-- Login Card -->
        <div class="bg-paper rounded-2xl p-6 sm:p-8 border border-line space-y-6">
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
                    placeholder="admin@poojacool.com"
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
                    <label class="flex items-center gap-2 text-ink-soft cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-line text-ink focus:ring-ink"
                        />
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <div class="pt-2">
                    <x-button
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="w-full"
                    >
                        MASUK KE DASHBOARD
                    </x-button>
                </div>
            </form>
        </div>

        <div class="text-center text-xs text-ink-mute">
            <a href="{{ route('home') }}" class="hover:text-ink transition underline underline-offset-4">
                &larr; Kembali ke Website Publik
            </a>
        </div>
    </div>

</body>
</html>
