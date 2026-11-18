@extends('layouts.app')

@section('title', 'Servis, Cuci & Instalasi AC di '.config('usaha.lokasi'))
@section('description', 'Servis, cuci, instalasi, perbaikan, dan isi freon AC rumahan di '.config('usaha.lokasi').'. Lihat daftar harga, pesan online, bayar via QRIS, transfer bank, atau e-wallet.')

@section('content')
<!-- 1. Hero -->
<section class="max-w-[1440px] mx-auto px-4 md:px-8 pt-4 pb-12 md:pb-20">
    <div class="rounded-[28px] overflow-hidden relative min-h-[460px] md:min-h-[680px] flex flex-col justify-between p-6 sm:p-10 md:p-16 bg-navy text-white">
        <!-- Foto unit AC + overlay navy solid 55% -->
        <div class="absolute inset-0 z-0">
            <img
                src="https://images.unsplash.com/photo-1568634697393-0165d25e7acb?auto=format&fit=crop&w=2000&q=80"
                alt="Deretan unit outdoor AC split terpasang rapi di dinding"
                width="2000"
                height="1333"
                fetchpriority="high"
                class="w-full h-full object-cover object-center"
            />
            <div class="absolute inset-0 bg-navy/55"></div>
        </div>

        <!-- Headline berundak -->
        <h1 class="relative z-10 pt-6 sm:pt-10 text-white font-heading font-semibold text-[40px] sm:text-5xl md:text-7xl lg:text-[94px] leading-[1.05] tracking-tight uppercase">
            <span class="block">UDARA SEJUK,</span>
            <span class="block mt-2 md:mt-4 sm:text-right">RUMAH NYAMAN<span class="text-ice">.</span></span>
        </h1>

        <!-- Blok info kiri bawah -->
        <div class="relative z-10 pt-10 max-w-xl space-y-5">
            <div class="space-y-1.5">
                <p class="text-white text-lg font-semibold">Servis &middot; Cuci &middot; Instalasi &middot; Perbaikan AC</p>
                <p class="text-ice text-[15px]">Melayani area {{ config('usaha.area_layanan') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <x-button href="{{ route('order.create') }}" variant="white" size="lg" class="w-full sm:w-auto">
                    PESAN SEKARANG
                </x-button>
                <x-button href="#layanan" variant="outline-white" size="lg" :arrow="false" class="w-full sm:w-auto">
                    LIHAT HARGA
                </x-button>
            </div>
        </div>
    </div>
</section>

<!-- 2. Tentang singkat -->
<section class="bg-paper py-20 md:py-28" data-reveal>
    <div class="max-w-[1440px] mx-auto px-4 md:px-8">
        <div class="max-w-2xl mb-12 md:mb-16">
            <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-3">TENTANG POOJA COOL</span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-ink leading-[1.1] tracking-tight">
                Bekerja Rapi, Harga Jelas di Awal.
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            <div class="lg:col-span-5">
                <div class="aspect-4/3 rounded-2xl overflow-hidden bg-ice">
                    <img
                        src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1200&q=80"
                        alt="Teknisi memeriksa jalur kelistrikan unit AC"
                        loading="lazy"
                        width="1200"
                        height="900"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div class="text-2xl sm:text-3xl md:text-4xl font-heading font-medium text-ink-mute leading-snug">
                    / Harga dihitung sistem sebelum Anda membayar.
                </div>
                <p class="text-ink text-base leading-relaxed max-w-xl">
                    Pilih layanan, lihat totalnya, lalu tentukan jadwal kunjungan. Teknisi datang membawa peralatan lengkap, memeriksa kondisi unit, dan menjelaskan temuan sebelum mengerjakan pekerjaan tambahan.
                </p>
                <div class="pt-2">
                    <x-button href="{{ route('about') }}" variant="secondary" size="md">
                        TENTANG KAMI
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Layanan & daftar harga per kategori -->
<section id="layanan" class="bg-ice py-20 md:py-28 scroll-mt-24" data-reveal>
    <div class="max-w-[1440px] mx-auto px-4 md:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 md:mb-16">
            <div class="max-w-2xl">
                <span class="text-xs uppercase tracking-[0.12em] text-ink font-medium block mb-3">LAYANAN & HARGA</span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-ink leading-[1.1] tracking-tight">
                    Daftar Harga Layanan.
                </h2>
            </div>
            <a href="{{ route('order.create') }}" class="self-start md:self-auto inline-flex items-center gap-1.5 text-xs uppercase tracking-wider font-semibold text-ink hover:underline underline-offset-4 group">
                <span>PESAN LAYANAN</span>
                <span class="transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5">↗</span>
            </a>
        </div>

        @if ($kategoriLayanan->isEmpty())
            <div class="bg-paper border border-line rounded-2xl p-6 sm:p-8 max-w-2xl">
                <h3 class="text-lg font-heading font-medium text-ink mb-2">Daftar harga sedang diperbarui.</h3>
                <p class="text-ink text-sm leading-relaxed">
                    Untuk menanyakan harga layanan, hubungi WhatsApp {{ \App\Support\WhatsApp::display(\App\Support\WhatsApp::adminNumber()) }}.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                @foreach ($kategoriLayanan as $kategori)
                    <article class="bg-paper border border-line rounded-2xl p-6 sm:p-8">
                        <h3 class="text-xl font-heading font-medium text-ink mb-4">{{ $kategori->nama }}</h3>

                        <ul class="divide-y divide-line">
                            @foreach ($kategori->services as $service)
                                <li class="py-3 flex items-baseline justify-between gap-4">
                                    <span class="text-sm text-ink leading-relaxed">{{ $service->name }}</span>
                                    <span class="text-sm font-semibold text-ink text-right whitespace-nowrap">{{ $service->priceLabel() }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @if ($kategori->catatan_harga)
                            <p class="pt-4 mt-1 border-t border-line text-xs text-ink-mute leading-relaxed">{{ $kategori->catatan_harga }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- 4. Nilai usaha -->
<section class="bg-paper py-20 md:py-28" data-reveal>
    <div class="max-w-[1440px] mx-auto px-4 md:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
            <div class="lg:col-span-5 space-y-4">
                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">KENAPA POOJA COOL</span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-ink leading-[1.1] tracking-tight">
                    Yang Anda Dapat Setiap Kunjungan.
                </h2>
                <p class="text-ink text-base leading-relaxed pt-2 max-w-md">
                    Cek kondisi dulu, jelaskan temuan, kerjakan setelah Anda setuju.
                </p>
            </div>

            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach ([
                    ['01', 'Teknisi Berpengalaman', 'Terbiasa menangani AC split, inverter, dan cassette untuk rumah maupun ruang usaha.'],
                    ['02', 'Harga Transparan', 'Biaya jasa dihitung otomatis dan terlihat sebelum Anda membayar. Sparepart, jika perlu diganti, dibayar di lokasi setelah Anda setujui.'],
                    ['03', 'Garansi Servis', 'Keluhan yang sama muncul lagi dalam masa garansi? Kami cek ulang tanpa biaya jasa.'],
                    ['04', 'Tepat Waktu & Rapi', 'Datang sesuai jadwal, lantai dan furnitur dilindungi selama pengerjaan.'],
                ] as [$no, $title, $desc])
                    <div class="bg-paper border border-line rounded-2xl p-6 sm:p-8 space-y-6">
                        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium">{{ $no }}</span>
                        <div>
                            <h3 class="text-lg font-heading font-medium text-ink mb-2">{{ $title }}</h3>
                            <p class="text-ink text-sm leading-relaxed">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- 5. Cara pesan -->
<section class="bg-ice py-20 md:py-28" data-reveal>
    <div class="max-w-[1440px] mx-auto px-4 md:px-8">
        <div class="max-w-2xl mb-12 md:mb-16">
            <span class="text-xs uppercase tracking-[0.12em] text-ink font-medium block mb-3">CARA PESAN</span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-ink leading-[1.1] tracking-tight">
                Empat Langkah, Selesai.
            </h2>
        </div>

        <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['Pilih Layanan', 'Pilih kategori, jenis pekerjaan, dan jumlah unit AC.'],
                ['Isi Data & Jadwal', 'Nama, nomor WhatsApp, alamat, dan tanggal kunjungan.'],
                ['Bayar', 'Bayar via QRIS, transfer bank, atau e-wallet, lalu unggah bukti bayar.'],
                ['Teknisi Datang', 'Setelah pembayaran diverifikasi, teknisi datang sesuai jadwal.'],
            ] as $i => [$title, $desc])
                <li class="bg-paper border border-line rounded-2xl p-6 sm:p-8 flex flex-col justify-between space-y-6">
                    <div class="text-5xl md:text-6xl font-heading font-medium text-blue select-none">{{ sprintf('%02d', $i + 1) }}</div>
                    <div>
                        <h3 class="text-lg font-heading font-medium text-ink mb-2">{{ $title }}</h3>
                        <p class="text-ink text-sm leading-relaxed">{{ $desc }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

<!-- 6. CTA penutup -->
<section class="max-w-[1440px] mx-auto px-4 md:px-8 pt-4">
    <div class="rounded-[28px] bg-navy text-white p-8 sm:p-14 md:p-20">
        <div class="max-w-2xl space-y-6">
            <span class="text-xs uppercase tracking-[0.12em] text-ice font-medium block">JADWALKAN KUNJUNGAN</span>
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-heading font-medium text-white leading-[1.08] tracking-tight">
                AC Kurang Dingin? Pesan Servis Hari Ini.
            </h2>
            <p class="text-ice text-base sm:text-lg leading-relaxed max-w-xl">
                Biaya jasa langsung terlihat sebelum membayar. Butuh tanya dulu? Hubungi WhatsApp {{ \App\Support\WhatsApp::display(\App\Support\WhatsApp::adminNumber()) }}, {{ \Illuminate\Support\Str::lcfirst(config('usaha.jam_operasional')) }}.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <x-button href="{{ route('order.create') }}" variant="white" size="lg">PESAN SEKARANG</x-button>
                <x-button href="{{ \App\Support\WhatsApp::adminLink() }}" variant="outline-white" size="lg" target="_blank" rel="noopener noreferrer">CHAT WHATSAPP</x-button>
            </div>
        </div>
    </div>
</section>

<!-- Schema.org -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "HVACBusiness",
  "name": @json(config('usaha.nama')),
  "legalName": @json(config('usaha.nama_legal')),
  "url": "{{ url('/') }}",
  "telephone": @json('+'.\App\Support\WhatsApp::normalize(config('usaha.telepon'))),
  "email": @json(config('usaha.email')),
  "areaServed": @json(config('usaha.area_layanan')),
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": @json(config('usaha.alamat')),
    "addressLocality": @json(config('usaha.lokasi')),
    "addressCountry": "ID"
  }
}
</script>
@endsection
