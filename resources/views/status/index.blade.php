@extends('layouts.app')

@section('title', 'Cek Status Pesanan')
@section('description', 'Lacak status pesanan dan pembayaran service AC Anda di POOJA COOL dengan kode order.')

@section('content')
<div class="py-12 md:py-20 bg-paper min-h-screen">
    <div class="max-w-[720px] mx-auto px-4 space-y-8">
        <!-- Search Card -->
        <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-4">
            <div>
                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">LACAK PESANAN</span>
                <h1 class="text-2xl sm:text-3xl font-heading font-medium text-ink leading-tight">
                    Cek Status Pesanan AC Anda
                </h1>
                <p class="text-xs text-ink-soft mt-1">
                    Masukkan kode order unik yang Anda terima saat memesan (Contoh: HVC-XXXXXXXX).
                </p>
            </div>

            <form method="GET" action="{{ route('order.status') }}" class="flex flex-col sm:flex-row gap-3 pt-2">
                <div class="flex-1">
                    <input
                        type="text"
                        name="code"
                        value="{{ $searchedCode ?? '' }}"
                        placeholder="Contoh: HVC-12345678"
                        required
                        class="w-full px-4 h-12 min-h-[48px] rounded-xl border-0 bg-paper text-ink font-mono uppercase tracking-wider text-sm placeholder:normal-case placeholder:font-sans focus:outline-none focus:ring-2 focus:ring-ink"
                    />
                </div>
                <x-button type="submit" variant="primary" size="md">
                    CARI PESANAN
                </x-button>
            </form>
        </div>

        @if ($searchedCode && ! $order)
            <!-- Not Found State -->
            <div class="bg-mist rounded-2xl p-8 text-center space-y-3">
                <span class="text-xs uppercase tracking-[0.12em] text-danger font-medium block">TIDAK DITEMUKAN</span>
                <h2 class="text-lg font-heading font-medium text-ink">Pesanan Tidak Ditemukan</h2>
                <p class="text-xs text-ink-soft max-w-sm mx-auto leading-relaxed">
                    Tidak ada pesanan dengan kode <span class="font-mono font-semibold text-ink">{{ $searchedCode }}</span>. Mohon periksa kembali kode Anda atau hubungi layanan pelanggan via WhatsApp.
                </p>
                <div class="pt-2">
                    <x-button href="{{ \App\Support\WhatsApp::adminLink('Halo Admin '.config('usaha.nama').', saya tidak menemukan pesanan dengan kode '.$searchedCode.'. Mohon bantuannya.') }}" target="_blank" rel="noopener noreferrer" variant="secondary" size="sm">
                        HUBUNGI CUSTOMER SERVICE
                    </x-button>
                </div>
            </div>
        @elseif ($order)
            <!-- Single Centralized Order Status Card -->
            <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-8">
                <!-- Header with Status Badge -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-line pb-6">
                    <div>
                        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">
                            KODE PESANAN &bull; {{ $order->code }}
                        </span>
                        <h2 class="text-2xl font-heading font-medium text-ink mt-1">
                            Status Progres Layanan
                        </h2>
                        <span class="text-xs text-ink-mute">Dipesan pada {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>

                    <div class="shrink-0">
                        <x-status-badge :status="$order->status" size="lg" />
                    </div>
                </div>

                <!-- Cancelled Alert -->
                @if ($order->status->value === 'dibatalkan')
                    <div class="p-4 rounded-xl bg-paper border border-line space-y-1.5">
                        <div class="text-xs font-semibold text-ink uppercase tracking-wider">
                            Pesanan Dibatalkan
                        </div>
                        <p class="text-sm text-ink leading-relaxed">
                            Alasan: {{ $order->alasan_batal ?? '-' }}
                        </p>
                        @if ($order->dibatalkan_pada)
                            <p class="text-xs text-ink-mute">Dibatalkan pada {{ $order->dibatalkan_pada->translatedFormat('d F Y, H:i') }} WIB</p>
                        @endif
                    </div>
                @endif

                <!-- Rejection Alert -->
                @if ($order->status->value === 'ditolak')
                    <div class="p-4 rounded-xl bg-danger/5 border border-danger/20 space-y-2">
                        <div class="text-xs font-semibold text-danger uppercase tracking-wider">
                            Bukti Pembayaran Ditolak oleh Admin
                        </div>
                        <p class="text-xs text-ink-soft leading-relaxed">
                            Alasan: "{{ $order->latestPayment?->note ?? 'Bukti pembayaran tidak sesuai nominal atau tidak valid.' }}"
                        </p>
                        @if ($order->latestPayment?->verified_at)
                            <p class="text-xs text-ink-soft leading-relaxed">
                                Unggah ulang paling lambat {{ $order->latestPayment->verified_at->copy()->addHours((int) config('booking.kedaluwarsa_jam', 24))->translatedFormat('d F Y, H:i') }} WIB, atau pesanan dibatalkan otomatis.
                            </p>
                        @endif
                        <div class="pt-2">
                            <x-button href="{{ route('payment.show', ['code' => $order->code]) }}" variant="primary" size="sm">
                                UNGGAH ULANG BUKTI BAYAR
                            </x-button>
                        </div>
                    </div>
                @endif

                <!-- Thin Vertical Timeline -->
                @php
                    $status = $order->status->value;
                    $hasProof = $order->payments()->exists();
                    $step1 = true;
                    $step2 = in_array($status, ['menunggu_verifikasi', 'disetujui', 'selesai']) || ($status === 'ditolak' && $hasProof);
                    $step3 = in_array($status, ['disetujui', 'selesai']);
                    $step4 = ($status === 'selesai');
                @endphp

                <div class="space-y-4">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">TIMELINE PENGERJAAN</span>

                    <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-line">
                        <!-- Step 1 -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full {{ $step1 ? 'bg-ink' : 'bg-steel-300' }} ring-4 ring-mist"></span>
                            <h3 class="text-sm font-medium text-ink">Pesanan Dibuat</h3>
                            <p class="text-xs text-ink-soft mt-0.5">Rincian layanan dan tanggal kunjungan tersimpan di sistem.</p>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full {{ $step2 ? 'bg-ink' : ($status === 'menunggu_pembayaran' ? 'bg-steel-300' : 'bg-line') }} ring-4 ring-mist"></span>
                            <h3 class="text-sm font-medium text-ink">Pembayaran & Bukti Transfer</h3>
                            <p class="text-xs text-ink-soft mt-0.5">
                                @if ($status === 'menunggu_pembayaran')
                                    Menunggu Anda mengunggah bukti pembayaran.
                                @elseif ($status === 'ditolak')
                                    Bukti ditolak. Silakan unggah ulang.
                                @else
                                    Bukti pembayaran telah berhasil diunggah.
                                @endif
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full {{ $step3 ? 'bg-ink' : ($status === 'menunggu_verifikasi' ? 'bg-steel-300' : 'bg-line') }} ring-4 ring-mist"></span>
                            <h3 class="text-sm font-medium text-ink">Verifikasi Admin</h3>
                            <p class="text-xs text-ink-soft mt-0.5">
                                {{ $step3 ? 'Pembayaran telah disetujui oleh admin.' : 'Admin sedang mencocokkan mutasi rekening Anda.' }}
                            </p>
                        </div>

                        <!-- Step 4 -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full {{ $step4 ? 'bg-ink' : ($step3 ? 'bg-steel-300' : 'bg-line') }} ring-4 ring-mist"></span>
                            <h3 class="text-sm font-medium text-ink">Kunjungan Teknisi Selesai</h3>
                            <p class="text-xs text-ink-soft mt-0.5">
                                {{ $step4 ? 'Teknisi telah menyelesaikan seluruh pekerjaan di lokasi.' : 'Teknisi akan hadir sesuai tanggal kunjungan.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Customer Details & Items Snapshot -->
                <div class="pt-6 border-t border-line space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">INFORMASI KUNJUNGAN</span>
                        <span class="text-[11px] text-ink-mute">Data pribadi disamarkan demi keamanan</span>
                    </div>

                    <div class="bg-paper rounded-xl p-5 border border-line space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-ink-mute">Nama Pemesan:</span>
                            <span class="font-medium text-ink">{{ mask_name($order->name) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-mute">WhatsApp / Telp:</span>
                            <span class="font-medium text-ink font-mono">{{ mask_phone($order->phone) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-mute">Tanggal Kunjungan:</span>
                            <span class="font-medium text-ink text-right">{{ $order->schedule_date->translatedFormat('l, d F Y') }}@if ($order->sesiLabel())<br><span class="text-ink-mute font-normal">Sesi {{ $order->sesiLabel() }}</span>@endif</span>
                        </div>
                        <div class="pt-2 border-t border-line/60">
                            <span class="text-ink-mute block mb-0.5">Alamat Kunjungan:</span>
                            <span class="text-ink leading-relaxed">{{ mask_address($order->address) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Services List & Total -->
                <div class="pt-4 border-t border-line space-y-4">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">RINCIAN BIAYA</span>

                    <div class="bg-paper rounded-xl p-5 border border-line space-y-3 text-xs">
                        <div class="divide-y divide-line/60">
                            @foreach ($order->items as $item)
                                <div class="py-2 first:pt-0 flex items-center justify-between">
                                    <span class="text-ink">{{ $item->service_name }} ({{ $item->qty }} {{ $item->service?->satuan ?? 'unit' }})</span>
                                    <span class="font-medium text-ink">{{ format_rupiah($item->subtotal) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-2 border-t border-line flex items-center justify-between text-ink-mute">
                            <span>Kode Unik Verifikasi:</span>
                            <span>+{{ format_rupiah($order->unique_code) }}</span>
                        </div>

                        <div class="pt-2 border-t border-line flex items-baseline justify-between">
                            <span class="text-xs uppercase tracking-wider text-ink-mute font-medium">TOTAL DIBAYAR:</span>
                            <span class="text-2xl font-heading font-medium text-ink">{{ format_rupiah($order->total) }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row gap-3">
                        @if ($order->status->value === 'menunggu_pembayaran')
                            <x-button href="{{ route('payment.show', ['code' => $order->code]) }}" variant="primary" size="md" class="w-full sm:flex-1">
                                BUKA HALAMAN PEMBAYARAN
                            </x-button>
                        @endif
                        <x-button href="{{ \App\Support\WhatsApp::adminLink('Halo Admin '.config('usaha.nama').', saya ingin menanyakan pesanan dengan kode '.$order->code.'.') }}" target="_blank" rel="noopener noreferrer" variant="secondary" size="md" class="w-full sm:flex-1">
                            TANYA ADMIN VIA WHATSAPP
                        </x-button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
