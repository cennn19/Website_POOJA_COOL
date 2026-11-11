@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-12 pb-20 md:py-24 bg-gradient-to-b from-primary-50 via-white to-neutral-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary-100 text-primary-700 text-xs font-semibold tracking-wide border border-primary-200">
                    <span class="w-2 h-2 rounded-full bg-primary-600"></span>
                    Solusi Pendingin Ruangan No. 1 di Jabodetabek
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-neutral-900 tracking-tight leading-tight">
                    Service AC Cepat, Rapi, <br class="hidden sm:inline">dan <span class="text-primary-600">Bergaransi 30 Hari</span>
                </h1>

                <p class="text-base sm:text-lg text-neutral-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Nikmati udara sejuk dan bersih tanpa khawatir. Teknisi HVAC profesional kami siap melayani cuci AC, isi freon, perbaikan kerusakan, dan instalasi unit baru langsung ke rumah Anda.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                    <x-button href="{{ route('order.create') }}" variant="accent" size="lg" class="w-full sm:w-auto shadow-md">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Pesan Sekarang
                    </x-button>

                    <x-button href="https://wa.me/6281234567890?text=Halo%20SejukAbadi,%20saya%20ingin%20konsultasi%20layanan%20AC" target="_blank" variant="secondary" size="lg" class="w-full sm:w-auto">
                        <svg class="w-5 h-5 text-success-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                        </svg>
                        Hubungi via WhatsApp
                    </x-button>
                </div>

                <!-- Trust Badges -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-neutral-200/80">
                    <div>
                        <div class="text-xl sm:text-2xl font-bold text-neutral-900">10.000+</div>
                        <div class="text-xs sm:text-sm text-neutral-600">Unit Terlayani</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold text-neutral-900">4.9 / 5.0</div>
                        <div class="text-xs sm:text-sm text-neutral-600">Rating Kepuasan</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold text-neutral-900">30 Hari</div>
                        <div class="text-xs sm:text-sm text-neutral-600">Garansi Servis</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <!-- AC Service Visual Card -->
                    <div class="bg-white rounded-3xl p-6 shadow-xl border border-neutral-200 space-y-5">
                        <div class="aspect-4/3 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-900 flex flex-col justify-between p-6 text-white relative overflow-hidden">
                            <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold">Standar Operasional HVACR</span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                            </div>
                            <div class="space-y-1 relative z-10">
                                <h3 class="text-xl font-bold">Teknisi Bersertifikat</h3>
                                <p class="text-xs text-primary-100">Peralatan modern bertekanan tinggi, plastik penutup higienis, dan vakum berstandar pabrikan.</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-neutral-50 border border-neutral-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs">✓</div>
                                    <span class="text-sm font-medium text-neutral-800">Cuci Bersih & Bebas Bakteri</span>
                                </div>
                                <span class="text-xs text-neutral-500 font-semibold">100% Bersih</span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-neutral-50 border border-neutral-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs">✓</div>
                                    <span class="text-sm font-medium text-neutral-800">Pemeriksaan Ampere & Tekanan</span>
                                </div>
                                <span class="text-xs text-neutral-500 font-semibold">Akurat</span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-neutral-50 border border-neutral-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs">✓</div>
                                    <span class="text-sm font-medium text-neutral-800">Pembayaran QRIS Mudah & Aman</span>
                                </div>
                                <span class="text-xs text-neutral-500 font-semibold">Instan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Services Section -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Layanan Unggulan</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-900 tracking-tight">Solusi Lengkap Perawatan AC Anda</h2>
            <p class="text-neutral-600 text-sm sm:text-base">Daftar layanan terfavorit dengan transparansi harga dan jaminan pengerjaan bersih tanpa meninggalkan kotoran di rumah Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($featuredServices as $service)
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-primary-500/50 transition duration-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $service->type->value === 'package' ? 'bg-primary-50 text-primary-600 border border-primary-200' : 'bg-neutral-100 text-neutral-700' }}">
                                {{ $service->type->label() }}
                            </span>
                            @if ($loop->first)
                                <span class="px-2.5 py-0.5 rounded-full bg-accent-500 text-white text-[11px] font-bold">Paling Laris</span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-neutral-900 group-hover:text-primary-600 transition">{{ $service->name }}</h3>
                            <p class="text-sm text-neutral-600 mt-2 leading-relaxed">{{ $service->description }}</p>
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-neutral-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-neutral-500 block">Mulai dari</span>
                            <span class="text-xl font-bold text-primary-900">{{ format_rupiah($service->price) }}</span>
                        </div>
                        <x-button href="{{ route('order.create') }}" variant="secondary" size="sm">
                            Pilih
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <x-button href="{{ route('order.create') }}" variant="primary" size="lg">
                Lihat Semua Paket & Layanan Satuan
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </x-button>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-16 md:py-24 bg-neutral-50 border-y border-neutral-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Keunggulan Kami</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-900 tracking-tight">Kenapa Memilih SejukAbadi?</h2>
            <p class="text-neutral-600 text-sm sm:text-base">Kami memberikan standar layanan penyejuk udara terbaik demi kenyamanan dan kesehatan keluarga Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl bg-white border border-neutral-200 shadow-sm space-y-3">
                <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900">Teknisi Ahli & Sopan</h3>
                <p class="text-sm text-neutral-600 leading-relaxed">Berpengalaman di industri pendingin udara, berpenampilan rapi, ramah, dan mematuhi etika bertamu.</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-neutral-200 shadow-sm space-y-3">
                <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900">Garansi Servis 30 Hari</h3>
                <p class="text-sm text-neutral-600 leading-relaxed">Jika AC mengalami masalah serupa dalam kurun 30 hari pasca pengerjaan, teknisi siap datang kembali gratis.</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-neutral-200 shadow-sm space-y-3">
                <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900">Harga Jujur & Pasti</h3>
                <p class="text-sm text-neutral-600 leading-relaxed">Semua tarif tercantum jelas di website. Tidak ada pungutan tersembunyi atau mark-up harga di lokasi.</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-neutral-200 shadow-sm space-y-3">
                <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900">Tepat Waktu Sesuai Jadwal</h3>
                <p class="text-sm text-neutral-600 leading-relaxed">Anda menentukan tanggal kunjungan. Tim kami mengonfirmasi dan tiba tepat waktu sesuai janji temu.</p>
            </div>
        </div>
    </div>
</section>

<!-- How to Order (4 Langkah) -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Cara Pemesanan</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-900 tracking-tight">4 Langkah Praktis Servis AC</h2>
            <p class="text-neutral-600 text-sm sm:text-base">Pesan mudah dari handphone tanpa perlu login atau unduh aplikasi tambahan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative">
            <div class="space-y-4 text-center sm:text-left">
                <div class="w-12 h-12 rounded-2xl bg-primary-600 text-white font-bold text-xl flex items-center justify-center shadow-md mx-auto sm:mx-0">
                    1
                </div>
                <h3 class="text-lg font-bold text-neutral-900">Pilih Layanan</h3>
                <p class="text-sm text-neutral-600 leading-relaxed">Pilih paket hemat atau satuan seperti cuci AC atau tambah freon sesuai kebutuhan unit Anda.</p>
            </div>

            <div class="space-y-4 text-center sm:text-left">
                <div class="w-12 h-12 rounded-2xl bg-primary-600 text-white font-bold text-xl flex items-center justify-center shadow-md mx-auto sm:mx-0">
                    2
                </div>
                <h3 class="text-lg font-bold text-neutral-900">Isi Data & Jadwal</h3>
                <p class="text-sm text-neutral-600 leading-relaxed">Masukkan nama, nomor WhatsApp aktif, alamat lengkap rumah, serta tanggal kedatangan yang diinginkan.</p>
            </div>

            <div class="space-y-4 text-center sm:text-left">
                <div class="w-12 h-12 rounded-2xl bg-primary-600 text-white font-bold text-xl flex items-center justify-center shadow-md mx-auto sm:mx-0">
                    3
                </div>
                <h3 class="text-lg font-bold text-neutral-900">Bayar QRIS & Upload</h3>
                <p class="text-sm text-neutral-600 leading-relaxed">Scan kode QRIS dari aplikasi bank/e-wallet mana pun, lalu unggah foto bukti transfer di halaman pembayaran.</p>
            </div>

            <div class="space-y-4 text-center sm:text-left">
                <div class="w-12 h-12 rounded-2xl bg-accent-500 text-white font-bold text-xl flex items-center justify-center shadow-md mx-auto sm:mx-0">
                    4
                </div>
                <h3 class="text-lg font-bold text-neutral-900">Teknisi Datang</h3>
                <p class="text-sm text-neutral-600 leading-relaxed">Admin memverifikasi pesanan Anda dan teknisi langsung berangkat ke alamat Anda tepat waktu.</p>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-16 md:py-24 bg-neutral-50 border-t border-neutral-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Dokumentasi Kerja</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-900 tracking-tight">Hasil Pengerjaan Tim Kami</h2>
            <p class="text-neutral-600 text-sm sm:text-base">Bukti pengerjaan rapi, bersih, dan sesuai standar keselamatan teknisi HVAC profesional.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="group overflow-hidden rounded-2xl bg-white border border-neutral-200 shadow-sm">
                <div class="aspect-4/3 bg-neutral-200 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10"></div>
                    <div class="text-center p-6 text-neutral-700 z-0">
                        <svg class="w-12 h-12 mx-auto text-primary-600 mb-2 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <span class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Foto Lapangan</span>
                    </div>
                    <div class="absolute bottom-3 left-4 right-4 z-20 text-white">
                        <span class="text-xs text-primary-300 font-medium">Cuci AC Split Residensial</span>
                        <h4 class="text-sm font-bold">Pembersihan Evaporator & Plastik Talang</h4>
                    </div>
                </div>
            </div>

            <div class="group overflow-hidden rounded-2xl bg-white border border-neutral-200 shadow-sm">
                <div class="aspect-4/3 bg-neutral-200 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10"></div>
                    <div class="text-center p-6 text-neutral-700 z-0">
                        <svg class="w-12 h-12 mx-auto text-primary-600 mb-2 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Foto Lapangan</span>
                    </div>
                    <div class="absolute bottom-3 left-4 right-4 z-20 text-white">
                        <span class="text-xs text-primary-300 font-medium">Instalasi Baru</span>
                        <h4 class="text-sm font-bold">Vakum Jalur Pipa & Pasang Outdoor Braket</h4>
                    </div>
                </div>
            </div>

            <div class="group overflow-hidden rounded-2xl bg-white border border-neutral-200 shadow-sm">
                <div class="aspect-4/3 bg-neutral-200 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10"></div>
                    <div class="text-center p-6 text-neutral-700 z-0">
                        <svg class="w-12 h-12 mx-auto text-primary-600 mb-2 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Foto Lapangan</span>
                    </div>
                    <div class="absolute bottom-3 left-4 right-4 z-20 text-white">
                        <span class="text-xs text-primary-300 font-medium">Uji Tekanan & Freon</span>
                        <h4 class="text-sm font-bold">Pengisian Freon R32 dengan Manifold Presisi</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Testimoni Pelanggan</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-900 tracking-tight">Kepuasan Pelanggan Nyata</h2>
            <p class="text-neutral-600 text-sm sm:text-base">Simak ulasan dari ratusan pemilik rumah dan kantor yang telah mempercayakan AC mereka kepada kami.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl border border-neutral-200 bg-neutral-50/50 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex text-amber-400 gap-1 text-sm">★★★★★</div>
                    <p class="text-sm text-neutral-700 leading-relaxed italic">
                        "Teknisi datang on time, bawa plastik pelindung rapi jadi dinding dan kasur aman gak kecipratan air kotor. AC langsung berasa dingin kayak waktu baru beli!"
                    </p>
                </div>
                <div class="pt-3 border-t border-neutral-200/60">
                    <div class="font-bold text-sm text-neutral-900">Bapak Hendra Wibowo</div>
                    <div class="text-xs text-neutral-500">Kebayoran Baru, Jakarta Selatan</div>
                </div>
            </div>

            <div class="p-6 rounded-2xl border border-neutral-200 bg-neutral-50/50 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex text-amber-400 gap-1 text-sm">★★★★★</div>
                    <p class="text-sm text-neutral-700 leading-relaxed italic">
                        "Suka banget sama sistem ordernya. Tinggal klik di web, bayar QRIS langsung diverifikasi tanpa ribet install aplikasi. Harganya juga jelas banget dari awal."
                    </p>
                </div>
                <div class="pt-3 border-t border-neutral-200/60">
                    <div class="font-bold text-sm text-neutral-900">Ibu Maya Anggraini</div>
                    <div class="text-xs text-neutral-500">BSD City, Tangerang Selatan</div>
                </div>
            </div>

            <div class="p-6 rounded-2xl border border-neutral-200 bg-neutral-50/50 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex text-amber-400 gap-1 text-sm">★★★★★</div>
                    <p class="text-sm text-neutral-700 leading-relaxed italic">
                        "AC kantor mendadak bocor air pas lagi jam kerja. Teknisi SejukAbadi tanggap langsung beres dalam 1 jam. Pelayanannya sangat profesional dan bergaransi."
                    </p>
                </div>
                <div class="pt-3 border-t border-neutral-200/60">
                    <div class="font-bold text-sm text-neutral-900">Rian Pratama</div>
                    <div class="text-xs text-neutral-500">Kelapa Gading, Jakarta Utara</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Accordion -->
<section class="py-16 md:py-24 bg-neutral-50 border-t border-neutral-200" x-data="{ active: null }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Pertanyaan Umum</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-900 tracking-tight">Kerap Ditanyakan (FAQ)</h2>
            <p class="text-neutral-600 text-sm sm:text-base">Jawaban atas pertanyaan umum seputar pemesanan dan layanan kami.</p>
        </div>

        <div class="space-y-3">
            <div class="rounded-2xl border border-neutral-200 bg-white overflow-hidden shadow-xs">
                <button
                    type="button"
                    @click="active = (active === 1 ? null : 1)"
                    class="w-full p-5 text-left flex items-center justify-between font-semibold text-neutral-900 hover:text-primary-600 transition"
                >
                    <span>Berapa lama garansi yang diberikan setelah servis?</span>
                    <svg class="w-5 h-5 text-neutral-400 transition-transform duration-200" :class="{ 'rotate-180': active === 1 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="active === 1" x-cloak class="px-5 pb-5 text-sm text-neutral-600 leading-relaxed border-t border-neutral-100 pt-3">
                    Kami memberikan garansi servis selama 30 hari penuh sejak tanggal pekerjaan selesai. Jika ada keluhan yang sama pada unit yang telah diperbaiki, teknisi kami akan datang melakukan pengecekan ulang tanpa dikenakan biaya jasa.
                </div>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white overflow-hidden shadow-xs">
                <button
                    type="button"
                    @click="active = (active === 2 ? null : 2)"
                    class="w-full p-5 text-left flex items-center justify-between font-semibold text-neutral-900 hover:text-primary-600 transition"
                >
                    <span>Apakah saya harus membuat akun untuk memesan layanan?</span>
                    <svg class="w-5 h-5 text-neutral-400 transition-transform duration-200" :class="{ 'rotate-180': active === 2 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="active === 2" x-cloak class="px-5 pb-5 text-sm text-neutral-600 leading-relaxed border-t border-neutral-100 pt-3">
                    Tidak perlu! Anda dapat langsung memilih layanan, mengisi alamat serta jadwal kunjungan, dan melakukan pembayaran via QRIS tanpa perlu mendaftar atau login akun. Status pesanan dapat dicek kapan saja menggunakan kode pesanan Anda.
                </div>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white overflow-hidden shadow-xs">
                <button
                    type="button"
                    @click="active = (active === 3 ? null : 3)"
                    class="w-full p-5 text-left flex items-center justify-between font-semibold text-neutral-900 hover:text-primary-600 transition"
                >
                    <span>Bagaimana cara melakukan pembayaran dengan QRIS?</span>
                    <svg class="w-5 h-5 text-neutral-400 transition-transform duration-200" :class="{ 'rotate-180': active === 3 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="active === 3" x-cloak class="px-5 pb-5 text-sm text-neutral-600 leading-relaxed border-t border-neutral-100 pt-3">
                    Setelah mengisi form pesanan, sistem akan menampilkan total pembayaran (termasuk 3 digit kode unik). Anda cukup membuka aplikasi mobile banking (BCA, Mandiri, BRI, BNI, dll) atau dompet digital (GoPay, OVO, Dana, ShopeePay), scan kode QRIS yang tersedia, bayar sesuai nominal pas, lalu unggah bukti transfer.
                </div>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white overflow-hidden shadow-xs">
                <button
                    type="button"
                    @click="active = (active === 4 ? null : 4)"
                    class="w-full p-5 text-left flex items-center justify-between font-semibold text-neutral-900 hover:text-primary-600 transition"
                >
                    <span>Apakah ada biaya tambahan transport untuk daerah tertentu?</span>
                    <svg class="w-5 h-5 text-neutral-400 transition-transform duration-200" :class="{ 'rotate-180': active === 4 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="active === 4" x-cloak class="px-5 pb-5 text-sm text-neutral-600 leading-relaxed border-t border-neutral-100 pt-3">
                    Untuk seluruh area jangkauan inti Jabodetabek, biaya transportasi sudah termasuk dalam harga layanan yang tertera di website. Tidak ada biaya siluman saat teknisi tiba di lokasi.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Closing CTA Section -->
<section class="py-16 md:py-20 bg-primary-900 text-white relative overflow-hidden">
    <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-primary-600/20 rounded-full blur-3xl"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10 space-y-6">
        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">Ruangan Kurang Dingin atau AC Bocor?</h2>
        <p class="text-neutral-300 text-base sm:text-lg max-w-2xl mx-auto">
            Jangan tunggu sampai kompresor rusak parah. Jadwalkan servis AC rumah Anda hari ini dengan teknisi profesional bergaransi.
        </p>
        <div class="pt-2">
            <x-button href="{{ route('order.create') }}" variant="accent" size="lg" class="shadow-lg">
                Pesan Servis Sekarang
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </x-button>
        </div>
    </div>
</section>

<!-- Schema.org LocalBusiness JSON-LD -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "LocalBusiness",
  "name": "SejukAbadi HVAC & AC Service",
  "image": "{{ asset('images/logo.png') }}",
  "telephone": "+6281234567890",
  "email": "layanan@sejukabadi.com",
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": "Jl. Raya Pendingin Sejuk No. 88, Kebayoran Baru",
    "addressLocality": "Jakarta Selatan",
    "addressRegion": "DKI Jakarta",
    "postalCode": "12180",
    "addressCountry": "ID"
  },
  "geo": {
    "@@type": "GeoCoordinates",
    "latitude": -6.2435,
    "longitude": 106.8021
  },
  "openingHoursSpecification": {
    "@@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"
    ],
    "opens": "08:00",
    "closes": "20:00"
  },
  "priceRange": "Rp 65.000 - Rp 450.000"
}
</script>
@endsection
