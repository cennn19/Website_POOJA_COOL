@extends('layouts.app')

@section('title', 'Kontak')
@section('description', 'Hubungi '.config('usaha.nama').' lewat WhatsApp, telepon, atau email untuk konsultasi dan jadwal service AC di '.config('usaha.lokasi').'.')

@section('content')
<section class="max-w-[1440px] mx-auto px-4 md:px-8 pt-10 md:pt-16 pb-20">
    <div class="max-w-2xl mb-12">
        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-3">LAYANAN KONSULTASI</span>
        <h1 class="text-3xl sm:text-5xl font-heading font-medium text-ink leading-[1.08] tracking-tight">
            Hubungi Customer Care {{ config('usaha.nama') }}.
        </h1>
        <p class="text-ink-soft text-base leading-relaxed mt-4">
            Punya pertanyaan teknis seputar AC Anda, ingin berkonsultasi mengenai keluhan pendinginan, atau membutuhkan kunjungan darurat? Tim kami siap melayani Anda.
        </p>
    </div>

    <!-- Two Columns Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        <!-- Left: Contact Details Cards (mist) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Direct WhatsApp Card -->
            <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-4">
                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">RESPON CEPAT</span>
                <h2 class="text-2xl font-heading font-medium text-ink">
                    WhatsApp Customer Care
                </h2>
                <p class="text-ink-soft text-sm leading-relaxed">
                    Respon rata-rata di bawah 5 menit pada jam operasional. Tanyakan ketersediaan teknisi hari ini atau konsultasikan keluhan AC Anda.
                </p>
                <div class="pt-2">
                    <x-button
                        href="{{ \App\Support\WhatsApp::adminLink('Halo '.config('usaha.nama').', saya ingin konsultasi layanan AC.') }}"
                        target="_blank"
                        variant="primary"
                        size="md"
                        class="w-full sm:w-auto"
                    >
                        CHAT WHATSAPP SEKARANG ({{ \App\Support\WhatsApp::display(\App\Support\WhatsApp::adminNumber()) }})
                    </x-button>
                </div>
            </div>

            <!-- Workshop & Operation Info -->
            <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-6">
                <div>
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">KANTOR & WORKSHOP</span>
                    <h3 class="text-base font-medium text-ink">{{ config('usaha.nama') }}</h3>
                    <p class="text-ink-soft text-sm leading-relaxed mt-1 select-text">
                        {{ config('usaha.alamat') }}
                    </p>
                </div>

                <div class="pt-4 border-t border-line/60">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">TELEPON</span>
                    <p class="text-ink text-sm font-medium select-text">{{ config('usaha.telepon') }}</p>
                </div>

                <div class="pt-4 border-t border-line/60">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">EMAIL RESMI</span>
                    <p class="text-ink text-sm font-medium select-text break-all">{{ config('usaha.email') }}</p>
                </div>

                <div class="pt-4 border-t border-line/60">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">JAM OPERASIONAL</span>
                    <p class="text-ink text-sm font-medium">{{ config('usaha.jam_operasional') }}</p>
                </div>

                <div class="pt-4 border-t border-line/60">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">JANGKAUAN WILAYAH</span>
                    <p class="text-ink-soft text-sm leading-relaxed">
                        {{ config('usaha.area_layanan') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: Inquiry Form & Quick Booking -->
        <div class="lg:col-span-7 bg-mist rounded-2xl p-6 sm:p-10 space-y-6">
            <div>
                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">PESAN LAYANAN LANGSUNG</span>
                <h2 class="text-2xl font-heading font-medium text-ink">
                    Lebih Cepat Melalui Sistem Order Online
                </h2>
                <p class="text-ink-soft text-sm leading-relaxed mt-1">
                    Anda dapat langsung memilih layanan, melihat kalkulasi total harga otomatis, dan memilih tanggal kunjungan tanpa harus menunggu antrean chat admin.
                </p>
            </div>

            <div class="p-6 rounded-xl bg-paper border border-line/80 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
                    <span class="text-xs uppercase tracking-wider font-semibold text-ink">Sistem Pemesanan 24/7 Aktif</span>
                </div>
                <p class="text-ink-soft text-sm leading-relaxed">
                    Pilih paket cuci, penambahan freon, perbaikan keluhan kompresor atau instalasi unit baru dengan harga transparan dan kode unik pembayaran otomatis (QRIS, transfer bank, atau e-wallet).
                </p>
                <div class="pt-2">
                    <x-button href="{{ route('order.create') }}" variant="primary" size="md">
                        BUKA HALAMAN ORDER SEKARANG
                    </x-button>
                </div>
            </div>

            <div class="pt-4 border-t border-line/60">
                <h3 class="text-sm font-medium text-ink mb-2">Sudah Memiliki Pesanan?</h3>
                <p class="text-ink-soft text-xs leading-relaxed mb-4">
                    Jika Anda sudah melakukan pemesanan dan ingin memantau status persetujuan pembayaran atau jadwal penugasan teknisi, gunakan fitur cek status order.
                </p>
                <x-button href="{{ route('order.status') }}" variant="secondary" size="sm">
                    CEK STATUS PESANAN ANDA
                </x-button>
            </div>
        </div>
    </div>
</section>
@endsection
