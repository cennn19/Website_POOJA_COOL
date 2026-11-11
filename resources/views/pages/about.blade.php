@extends('layouts.app')

@section('content')
<div class="py-12 md:py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <!-- Breadcrumb / Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Tentang SejukAbadi</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-neutral-900 tracking-tight">Dedikasi Memberikan Udara Sehat dan Dingin Optimal</h1>
            <p class="text-neutral-600 text-base sm:text-lg leading-relaxed">
                Berdiri sejak 2018, SejukAbadi hadir menjadi mitra andalan keluarga dan pelaku usaha di wilayah Jabodetabek dalam pemeliharaan dan reparasi sistem pendingin ruangan (HVACR).
            </p>
        </div>

        <!-- Achievement Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">
            <div class="p-6 rounded-2xl bg-neutral-50 border border-neutral-200 text-center space-y-1">
                <div class="text-3xl sm:text-4xl font-extrabold text-primary-600">8+</div>
                <div class="text-sm font-semibold text-neutral-900">Tahun Pengalaman</div>
                <div class="text-xs text-neutral-500">Sejak tahun 2018</div>
            </div>

            <div class="p-6 rounded-2xl bg-neutral-50 border border-neutral-200 text-center space-y-1">
                <div class="text-3xl sm:text-4xl font-extrabold text-primary-600">10.000+</div>
                <div class="text-sm font-semibold text-neutral-900">Unit Servis Selesai</div>
                <div class="text-xs text-neutral-500">Rumah tinggal & kantor</div>
            </div>

            <div class="p-6 rounded-2xl bg-neutral-50 border border-neutral-200 text-center space-y-1">
                <div class="text-3xl sm:text-4xl font-extrabold text-primary-600">20+</div>
                <div class="text-sm font-semibold text-neutral-900">Teknisi Tersertifikasi</div>
                <div class="text-xs text-neutral-500">Pelatihan standar BNSP/SNI</div>
            </div>

            <div class="p-6 rounded-2xl bg-neutral-50 border border-neutral-200 text-center space-y-1">
                <div class="text-3xl sm:text-4xl font-extrabold text-primary-600">99%</div>
                <div class="text-sm font-semibold text-neutral-900">Kepuasan Pelanggan</div>
                <div class="text-xs text-neutral-500">Berdasarkan ulasan nyata</div>
            </div>
        </div>

        <!-- Story Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-20">
            <div class="lg:col-span-6 space-y-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-semibold">
                    Kisah Kami
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-neutral-900 tracking-tight">Menjawab Keraguan Servis AC dengan Standar Profesional</h2>
                <p class="text-neutral-600 text-sm sm:text-base leading-relaxed">
                    Seringkali pemilik rumah merasa khawatir dengan jasa servis AC yang tidak transparan, biaya yang tiba-tiba melambung saat pengerjaan, atau hasil cuci yang merusak dinding dan interior.
                </p>
                <p class="text-neutral-600 text-sm sm:text-base leading-relaxed">
                    SejukAbadi didirikan untuk mendobrak stigma tersebut. Kami menerapkan Standard Operating Procedure (SOP) yang ketat: harga baku tanpa biaya tersembunyi, peralatan vakum dan pompa cuci bertekanan presisi, perlindungan penuh pada area kerja, serta garansi resmi tertulis selama 30 hari.
                </p>
            </div>

            <div class="lg:col-span-6">
                <div class="rounded-3xl bg-gradient-to-br from-primary-900 to-primary-700 p-8 text-white space-y-6 shadow-xl">
                    <h3 class="text-xl font-bold border-b border-primary-600/50 pb-4">Komitmen Standar Kerja Kami</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-accent-500 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                            <div>
                                <h4 class="font-bold text-sm">Transparansi 100%</h4>
                                <p class="text-xs text-primary-100 mt-0.5">Penjelasan teknis jujur apakah freon memang perlu diisi atau hanya filter kotor yang mampet.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-accent-500 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                            <div>
                                <h4 class="font-bold text-sm">Higienitas & Kebersihan Ruangan</h4>
                                <p class="text-xs text-primary-100 mt-0.5">Plastik terpal penampung air cuci tebal, tidak menetes ke lantai atau perabotan rumah tangga Anda.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-accent-500 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                            <div>
                                <h4 class="font-bold text-sm">Suku Cadang & Refrigeran Berkualitas</h4>
                                <p class="text-xs text-primary-100 mt-0.5">Menggunakan freon murni resmi (R32, R410A) tanpa campuran yang merusak kompresor.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Area -->
        <div class="rounded-3xl bg-neutral-50 border border-neutral-200 p-8 sm:p-12 text-center space-y-6">
            <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Cakupan Wilayah</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-neutral-900 tracking-tight">Melayani Seluruh Penjuru Jabodetabek</h2>
            <p class="text-neutral-600 text-sm sm:text-base max-w-2xl mx-auto">
                Armada teknisi kami tersebar di pos-pos strategis Jakarta, Bogor, Depok, Tangerang, dan Bekasi, memungkinkan respon kedatangan yang cepat ke lokasi Anda.
            </p>
            <div class="flex justify-center pt-2">
                <x-button href="{{ route('order.create') }}" variant="accent" size="lg">
                    Pesan Jadwal Kunjungan Teknisi
                </x-button>
            </div>
        </div>
    </div>
</div>
@endsection
