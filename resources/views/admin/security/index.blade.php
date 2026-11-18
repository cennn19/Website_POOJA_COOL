@extends('layouts.admin')

@section('title', 'Cek Keamanan')

@section('content')
<div class="space-y-8 max-w-3xl">
    <div>
        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">SERVER</span>
        <h1 class="text-2xl sm:text-3xl font-heading font-medium text-ink">Cek Keamanan</h1>
        <p class="text-sm text-ink mt-2">
            Pemeriksaan konfigurasi server. Buka halaman ini setiap selesai upload/deploy.
            Semua baris <strong>wajib</strong> harus berstatus <strong>LOLOS</strong> sebelum website dibuka untuk umum.
        </p>
    </div>

    @if ($failures > 0)
        <x-alert type="danger"><strong>{{ $failures }} pemeriksaan wajib gagal.</strong> Perbaiki sesuai petunjuk di bawah.</x-alert>
    @else
        <x-alert type="success"><strong>Semua pemeriksaan wajib lolos.</strong></x-alert>
    @endif

    <div class="bg-paper rounded-2xl border border-line divide-y divide-line">
        @foreach ($checks as $check)
            <div class="p-4 sm:px-6 flex gap-4 items-start">
                <span @class([
                    'shrink-0 mt-0.5 text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full',
                    'bg-success/10 text-success' => $check['status'] === 'lolos',
                    'bg-danger/10 text-danger' => $check['status'] === 'gagal',
                    'bg-warning/10 text-warning' => $check['status'] === 'saran',
                ])>{{ $check['status'] }}</span>
                <div class="text-sm">
                    <p class="text-ink">{{ $check['name'] }}</p>
                    @if ($check['advice'])
                        <p class="text-ink-mute mt-0.5">{{ $check['advice'] }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="space-y-3">
        <h2 class="text-base font-heading font-medium text-ink">Diagnosa koneksi</h2>
        @if ($proxyWarning)
            <x-alert type="warning">
                Server berada di belakang proxy (ada header X-Forwarded-For), tetapi <code>TRUSTED_PROXIES</code> belum diisi.
                Akibatnya semua pengunjung terlihat ber-IP sama dan batas akses berlaku bersama.
                Isi <code>TRUSTED_PROXIES=*</code> di .env, lalu muat ulang halaman ini dan pastikan
                "IP pengunjung menurut aplikasi" sama dengan IP Anda.
            </x-alert>
        @endif
        <div class="bg-paper rounded-2xl border border-line divide-y divide-line text-sm">
            @foreach ($diagnostics as $label => $value)
                <div class="p-3 sm:px-6 flex justify-between gap-4">
                    <span class="text-ink-mute">{{ $label }}</span>
                    <span class="text-ink font-mono text-right break-all">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
