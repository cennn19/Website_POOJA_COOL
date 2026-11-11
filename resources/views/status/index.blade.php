@extends('layouts.app')

@section('content')
<div class="py-8 md:py-14 bg-neutral-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-8">
        <!-- Search Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-neutral-200 shadow-sm space-y-4">
            <div class="space-y-1">
                <span class="text-primary-600 font-semibold text-xs uppercase tracking-wider">Lacak Pesanan</span>
                <h1 class="text-xl sm:text-2xl font-bold text-neutral-900">Cek Status Pesanan AC Anda</h1>
                <p class="text-xs text-neutral-500">Masukkan kode order unik yang Anda dapatkan saat memesan (Contoh: HVC-XXXXXXXX).</p>
            </div>

            <form method="GET" action="{{ route('order.status') }}" class="flex flex-col sm:flex-row gap-3 pt-2">
                <div class="flex-1">
                    <input
                        type="text"
                        name="code"
                        value="{{ $searchedCode ?? '' }}"
                        placeholder="Masukkan Kode Order (Contoh: HVC-12345678)"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:outline-none focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 text-neutral-900 font-mono tracking-wider uppercase text-base placeholder:normal-case placeholder:font-sans"
                    />
                </div>
                <button
                    type="submit"
                    class="px-6 py-3 rounded-xl font-bold text-white bg-primary-600 hover:bg-primary-700 transition flex items-center justify-center gap-2 cursor-pointer shrink-0 shadow-sm min-h-[44px]"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Cari Pesanan</span>
                </button>
            </form>
        </div>

        @if ($searchedCode && ! $order)
            <div class="bg-white rounded-3xl p-8 border border-neutral-200 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-danger-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-neutral-900">Pesanan Tidak Ditemukan</h2>
                <p class="text-sm text-neutral-600 max-w-md mx-auto">
                    Tidak ada pesanan dengan kode <span class="font-mono font-bold text-neutral-900">{{ $searchedCode }}</span>. Mohon periksa kembali ejaan kode Anda atau hubungi layanan pelanggan kami via WhatsApp.
                </p>
                <div class="pt-2">
                    <x-button href="https://wa.me/6281234567890" target="_blank" variant="secondary" size="sm">
                        Bantuan Customer Service
                    </x-button>
                </div>
            </div>
        @elseif ($order)
            <!-- Order Details Result -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-neutral-200 shadow-sm space-y-8">
                <!-- Header Info -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-neutral-100 pb-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-neutral-500 font-semibold uppercase tracking-wider">Kode:</span>
                            <span class="text-lg font-extrabold text-primary-900 font-mono tracking-wider">{{ $order->code }}</span>
                        </div>
                        <h2 class="text-xl font-bold text-neutral-900 mt-1">Status Progres Layanan</h2>
                        <span class="text-xs text-neutral-500">Dibuat pada {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>

                    <div class="shrink-0 flex items-center gap-3">
                        <x-status-badge :status="$order->status" size="lg" />
                    </div>
                </div>

                <!-- Rejection Alert & Action if rejected -->
                @if ($order->status->value === 'ditolak')
                    <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 space-y-3">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-danger-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="flex-1 space-y-1">
                                <h3 class="text-sm font-bold text-rose-950">Bukti Pembayaran Ditolak oleh Admin</h3>
                                <p class="text-xs text-rose-900 leading-relaxed">
                                    Catatan Admin: "<strong>{{ $order->latestPayment?->note ?? 'Bukti pembayaran tidak sesuai atau tidak valid.' }}</strong>"
                                </p>
                            </div>
                        </div>
                        <div class="pt-1">
                            <x-button href="{{ route('payment.show', ['code' => $order->code]) }}" variant="accent" size="sm">
                                Upload Ulang Bukti Pembayaran
                            </x-button>
                        </div>
                    </div>
                @endif

                <!-- Visual 4-Step Timeline -->
                @php
                    $status = $order->status->value;
                    $hasProof = $order->payments()->exists();
                    $step1 = true;
                    $step2 = in_array($status, ['menunggu_verifikasi', 'disetujui', 'selesai']) || ($status === 'ditolak' && $hasProof);
                    $step3 = in_array($status, ['disetujui', 'selesai']);
                    $step4 = ($status === 'selesai');
                @endphp

                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-neutral-900 uppercase tracking-wider">Timeline Proses</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Step 1 -->
                        <div class="p-4 rounded-2xl border {{ $step1 ? 'border-primary-500 bg-primary-50/50' : 'border-neutral-200 bg-white' }} space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-primary-600 text-white font-bold text-xs flex items-center justify-center">✓</span>
                                <span class="text-xs font-bold text-neutral-900">1. Pesanan Dibuat</span>
                            </div>
                            <p class="text-[11px] text-neutral-500">Layanan & jadwal tersimpan.</p>
                        </div>

                        <!-- Step 2 -->
                        <div class="p-4 rounded-2xl border {{ $step2 ? 'border-primary-500 bg-primary-50/50' : 'border-neutral-200 bg-white' }} space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full {{ $step2 ? 'bg-primary-600 text-white' : 'bg-neutral-200 text-neutral-600' }} font-bold text-xs flex items-center justify-center">
                                    {{ $step2 ? '✓' : '2' }}
                                </span>
                                <span class="text-xs font-bold text-neutral-900">2. Pembayaran</span>
                            </div>
                            <p class="text-[11px] text-neutral-500">
                                @if ($status === 'menunggu_pembayaran')
                                    Menunggu unggah bukti.
                                @elseif ($status === 'ditolak')
                                    Bukti ditolak.
                                @else
                                    Bukti terunggah.
                                @endif
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="p-4 rounded-2xl border {{ $step3 ? 'border-primary-500 bg-primary-50/50' : 'border-neutral-200 bg-white' }} space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full {{ $step3 ? 'bg-primary-600 text-white' : 'bg-neutral-200 text-neutral-600' }} font-bold text-xs flex items-center justify-center">
                                    {{ $step3 ? '✓' : '3' }}
                                </span>
                                <span class="text-xs font-bold text-neutral-900">3. Diverifikasi</span>
                            </div>
                            <p class="text-[11px] text-neutral-500">
                                {{ $step3 ? 'Admin telah menyetujui.' : 'Pemeriksaan mutasi admin.' }}
                            </p>
                        </div>

                        <!-- Step 4 -->
                        <div class="p-4 rounded-2xl border {{ $step4 ? 'border-primary-500 bg-primary-50/50' : 'border-neutral-200 bg-white' }} space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full {{ $step4 ? 'bg-primary-600 text-white' : 'bg-neutral-200 text-neutral-600' }} font-bold text-xs flex items-center justify-center">
                                    {{ $step4 ? '✓' : '4' }}
                                </span>
                                <span class="text-xs font-bold text-neutral-900">4. Teknisi Selesai</span>
                            </div>
                            <p class="text-[11px] text-neutral-500">
                                {{ $step4 ? 'Pekerjaan tuntas.' : 'Kunjungan & pengerjaan.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order and Customer Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-neutral-100 text-sm">
                    <!-- Customer and Schedule -->
                    <div class="space-y-3 p-5 rounded-2xl bg-neutral-50 border border-neutral-200">
                        <h4 class="font-bold text-neutral-900">Informasi Pemesan & Kunjungan</h4>
                        <div class="space-y-1.5 text-xs text-neutral-700">
                            <div><span class="text-neutral-500">Nama Pelanggan:</span> <strong class="text-neutral-900">{{ $order->name }}</strong></div>
                            <div><span class="text-neutral-500">Nomor Telepon:</span> {{ $order->phone }}</div>
                            <div><span class="text-neutral-500">Tanggal Kunjungan:</span> <strong class="text-primary-700">{{ $order->schedule_date->translatedFormat('d F Y') }}</strong></div>
                            <div><span class="text-neutral-500">Alamat:</span> {{ $order->address }}</div>
                            @if ($order->notes)
                                <div><span class="text-neutral-500">Catatan:</span> {{ $order->notes }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Items & Payment Total -->
                    <div class="space-y-3 p-5 rounded-2xl bg-neutral-50 border border-neutral-200">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-neutral-900">Layanan Dipesan</h4>
                            <span class="text-xs text-neutral-500">{{ $order->items->count() }} jenis</span>
                        </div>
                        <div class="space-y-2 text-xs divide-y divide-neutral-200/60">
                            @foreach ($order->items as $item)
                                <div class="pt-1.5 first:pt-0 flex items-center justify-between">
                                    <span>{{ $item->service_name }} ({{ $item->qty }}x)</span>
                                    <span class="font-semibold text-neutral-900">{{ format_rupiah($item->subtotal) }}</span>
                                </div>
                            @endforeach
                            <div class="pt-2 flex items-center justify-between text-neutral-600">
                                <span>Kode Unik QRIS:</span>
                                <span>+{{ format_rupiah($order->unique_code) }}</span>
                            </div>
                            <div class="pt-2 flex items-center justify-between text-sm font-bold text-neutral-900">
                                <span>Total Tagihan:</span>
                                <span class="text-primary-900 text-base font-extrabold">{{ format_rupiah($order->total) }}</span>
                            </div>
                        </div>

                        @if ($order->status->value === 'menunggu_pembayaran')
                            <div class="pt-2">
                                <x-button href="{{ route('payment.show', ['code' => $order->code]) }}" variant="accent" size="sm" class="w-full">
                                    Buka Halaman Pembayaran QRIS
                                </x-button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
