@extends('layouts.app')

@section('content')
<div class="py-12 md:py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto space-y-4 mb-16">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Hubungi Kami</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-neutral-900 tracking-tight">Kami Siap Melayani Kebutuhan AC Anda</h1>
            <p class="text-neutral-600 text-base sm:text-lg">
                Punya pertanyaan, ingin berkonsultasi seputar kerusakan AC, atau membutuhkan penanganan darurat? Hubungi customer care kami langsung.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Contact Info Cards -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Highlight WhatsApp Card -->
                <div class="p-6 rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-white shadow-lg space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Chat WhatsApp Cepat</h3>
                            <span class="text-xs text-emerald-100">Respon dalam < 5 menit</span>
                        </div>
                    </div>
                    <p class="text-sm text-emerald-50 leading-relaxed">
                        Konsultasi gratis seputar kendala AC Anda atau tanyakan ketersediaan jadwal hari ini dengan admin kami.
                    </p>
                    <a
                        href="https://wa.me/6281234567890?text=Halo%20SejukAbadi,%20saya%20ingin%20tanya%20layanan%20AC"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center w-full py-3 px-4 rounded-xl bg-white text-emerald-800 font-bold text-sm shadow hover:bg-emerald-50 transition"
                    >
                        Buka WhatsApp Sekarang (0812-3456-7890)
                    </a>
                </div>

                <!-- Office & Operating Hours -->
                <div class="p-6 rounded-3xl bg-neutral-50 border border-neutral-200 space-y-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-bold text-neutral-900 text-sm">Alamat Kantor Pusat</h4>
                            <p class="text-sm text-neutral-600 leading-relaxed">
                                Jl. Raya Pendingin Sejuk No. 88, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12180
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-bold text-neutral-900 text-sm">Jam Operasional Layanan</h4>
                            <p class="text-sm text-neutral-600">
                                Senin - Minggu: 08.00 - 20.00 WIB
                            </p>
                            <p class="text-xs text-primary-600 font-semibold">
                                Tetap buka pada hari libur nasional
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-bold text-neutral-900 text-sm">Email Resmi</h4>
                            <p class="text-sm text-neutral-600">
                                layanan@sejukabadi.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map / Interactive Action Section -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Location Map Preview Box -->
                <div class="rounded-3xl border border-neutral-200 overflow-hidden bg-neutral-100 shadow-sm">
                    <div class="h-64 sm:h-80 bg-neutral-200 flex flex-col items-center justify-center text-center p-6 space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-primary-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-neutral-900">Area Layanan Utama: Jabodetabek</h4>
                            <p class="text-xs text-neutral-600 max-w-sm mt-1">Armada teknisi kami siap diberangkatkan langsung ke rumah atau kantor Anda di seluruh wilayah Jabodetabek.</p>
                        </div>
                        <a
                            href="https://maps.google.com/?q=Jakarta"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-neutral-300 text-xs font-semibold text-neutral-800 hover:bg-neutral-50 shadow-xs"
                        >
                            <span>Buka Google Maps</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Call to action card -->
                <div class="p-8 rounded-3xl bg-primary-50 border border-primary-200 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="space-y-1 text-center sm:text-left">
                        <h3 class="text-lg font-bold text-primary-900">Ingin Langsung Pesan Servis?</h3>
                        <p class="text-sm text-neutral-600">Pilih layanan, tentukan tanggal, dan teknisi kami akan segera meluncur.</p>
                    </div>
                    <x-button href="{{ route('order.create') }}" variant="accent" size="lg" class="shrink-0 w-full sm:w-auto">
                        Mulai Pesan Sekarang
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
