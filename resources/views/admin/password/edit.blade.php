@extends('layouts.admin')

@section('title', 'Ganti Password')

@section('content')
<div class="space-y-8 max-w-xl">
    <div>
        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">AKUN</span>
        <h1 class="text-2xl sm:text-3xl font-heading font-medium text-ink">Ganti Password</h1>
        <p class="text-sm text-ink mt-2">
            Minimal 10 karakter, berisi huruf dan angka. Setelah diganti, perangkat lain yang masih login
            dengan akun ini akan dikeluarkan otomatis.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.password.update') }}" class="bg-paper rounded-2xl p-6 border border-line space-y-5">
        @csrf
        @method('PUT')

        <x-input label="Password saat ini" name="current_password" type="password" required autocomplete="current-password" />
        <x-input label="Password baru" name="password" type="password" required autocomplete="new-password" />
        <x-input label="Ulangi password baru" name="password_confirmation" type="password" required autocomplete="new-password" />

        <div class="pt-2">
            <x-button type="submit" variant="primary" size="md" :arrow="false">SIMPAN PASSWORD BARU</x-button>
        </div>
    </form>
</div>
@endsection
