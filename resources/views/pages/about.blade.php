@extends('layouts.app')

@section('title', 'Tentang Kami')
@section('description', 'Kenali '.config('usaha.nama_legal').', jasa servis, cuci, instalasi, dan perbaikan AC rumahan di '.config('usaha.lokasi').'. Harga jelas di awal, bayar via QRIS, transfer bank, atau e-wallet.')

@section('content')
<!-- 1. Header -->
<section class="max-w-[1440px] mx-auto px-4 md:px-8 pt-10 md:pt-16 pb-16 md:pb-24">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
        <div class="lg:col-span-7">
            <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-3">TENTANG POOJA COOL</span>
            <h1 class="text-[40px] sm:text-5xl md:text-6xl font-heading font-semibold text-ink leading-[1.05] tracking-tight uppercase">
                Servis AC yang<br class="hidden sm:inline"> jujur &amp; rapi<span class="text-blue">.</span>
            </h1>
            <p class="text-ink text-base sm:text-lg leading-relaxed mt-6 max-w-xl">
                {{ config('usaha.nama') }} adalah jasa servis, cuci, instalasi, dan perbaikan AC rumahan di {{ config('usaha.lokasi') }}. Kami membuat proses pesan servis AC sesederhana belanja online: pilih layanan, lihat harganya, bayar, teknisi datang.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 mt-8">
                <x-button href="{{ route('order.create') }}" variant="primary" size="lg" class="w-full sm:w-auto">
                    PESAN LAYANAN
                </x-button>
                <x-button href="{{ route('contact') }}" variant="secondary" size="lg" :arrow="false" class="w-full sm:w-auto">
                    HUBUNGI KAMI
                </x-button>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="rounded-2xl overflow-hidden bg-ice aspect-[4/3] lg:aspect-[4/5]">
                <img
                    src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=80"
                    alt="Teknisi memeriksa jalur kelistrikan unit AC"
                    width="1000"
                    height="1250"
                    class="w-full h-full object-cover"
                />
            </div>
        </div>
    </div>

    <!-- Fakta usaha -->
    <dl class="mt-12 md:mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-line border border-line rounded-2xl overflow-hidden">
        @foreach ([
            ['BADAN USAHA', config('usaha.nama_legal')],
            ['AREA LAYANAN', config('usaha.area_layanan')],
            ['JAM OPERASIONAL', config('usaha.jam_operasional')],
            ['PEMBAYARAN', 'QRIS, transfer bank, e-wallet'],
        ] as [$label, $value])
            <div class="bg-paper p-5 md:p-6">
                <dt class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium mb-1.5">{{ $label }}</dt>
                <dd class="text-base font-semibold text-ink">{{ $value }}</dd>
            </div>
        @endforeach
    </dl>
</section>

<!-- 2. Cerita & perbandingan -->
<section class="bg-ice py-20 md:py-28" data-reveal>
    <div class="max-w-[1440px] mx-auto px-4 md:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
            <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-5">
                <span class="text-xs uppercase tracking-[0.12em] text-ink font-medium block">KENAPA KAMI ADA</span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-ink leading-[1.1] tracking-tight">
                    Servis AC Tidak Harus Bikin Was&#8209;Was.
                </h2>
                <p class="text-ink text-base leading-relaxed">
                    Banyak pemilik rumah pernah mengalaminya: freon katanya habis tanpa bukti, biaya tambahan muncul di akhir, dan lantai basah setelah AC dicuci.
                </p>
                <p class="text-ink text-base leading-relaxed">
                    POOJA COOL dibangun untuk menghapus keraguan itu. Harga dihitung sistem sejak awal, kondisi unit ditunjukkan langsung, dan area kerja dikembalikan bersih.
                </p>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-paper border border-line rounded-2xl overflow-hidden">
                    <div class="hidden sm:grid grid-cols-2 border-b border-line">
                        <div class="px-6 py-4 text-xs uppercase tracking-[0.12em] font-semibold text-ink-mute">Yang sering terjadi</div>
                        <div class="px-6 py-4 text-xs uppercase tracking-[0.12em] font-semibold text-blue border-l border-line">Cara POOJA COOL</div>
                    </div>

                    <ul class="divide-y divide-line">
                        @foreach ([
                            ['Freon disebut habis tanpa bukti.', 'Tekanan freon diukur dengan manifold dan angkanya ditunjukkan ke Anda.'],
                            ['Biaya tambahan muncul di akhir.', 'Biaya jasa terlihat sebelum bayar. Sparepart yang perlu diganti dibayar di lokasi setelah Anda setujui.'],
                            ['Lantai dan kasur basah terkena cipratan.', 'Area kerja ditutup plastik dan dibersihkan sebelum teknisi pulang.'],
                            ['Jadwal tidak pasti, harus terus menanyakan.', 'Tanggal kunjungan dipilih saat pesan, status bisa dicek sendiri secara online.'],
                        ] as [$problem, $solution])
                            <li class="grid grid-cols-1 sm:grid-cols-2">
                                <div class="px-6 pt-5 sm:py-5">
                                    <span class="sm:hidden block text-[11px] uppercase tracking-[0.12em] font-semibold text-ink-mute mb-1">Yang sering terjadi</span>
                                    <p class="text-sm text-ink-mute leading-relaxed line-through decoration-line">{{ $problem }}</p>
                                </div>
                                <div class="px-6 pt-3 pb-5 sm:py-5 sm:border-l sm:border-line">
                                    <span class="sm:hidden block text-[11px] uppercase tracking-[0.12em] font-semibold text-blue mb-1">Cara POOJA COOL</span>
                                    <p class="text-sm text-ink font-medium leading-relaxed">{{ $solution }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Standar kerja -->
<section class="bg-paper py-20 md:py-28" data-reveal>
    <div class="max-w-[1440px] mx-auto px-4 md:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 md:mb-16">
            <div class="max-w-2xl">
                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-3">STANDAR KERJA</span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-ink leading-[1.1] tracking-tight">
                    Empat Hal yang Selalu Kami Lakukan.
                </h2>
            </div>
            <p class="text-ink text-base leading-relaxed max-w-md">
                Berlaku untuk setiap kunjungan, dari cuci AC satu unit sampai instalasi unit baru.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['Diagnosa Terbuka', 'Kondisi freon, kapasitor, dan kelistrikan diperiksa di depan Anda, lalu dijelaskan sebelum dikerjakan.'],
                ['Bersih & Rapi', 'Dinding, kasur, dan perabot terdekat dilindungi plastik kedap air selama pengerjaan.'],
                ['Refrigeran Sesuai Unit', 'Penambahan freon memakai jenis yang sesuai spesifikasi unit (R32 atau R410A).'],
                ['Garansi Servis', 'Keluhan yang sama muncul lagi dalam masa garansi? Teknisi datang mengecek ulang tanpa biaya jasa.'],
            ] as $i => [$title, $desc])
                <div class="bg-paper border border-line rounded-2xl p-6 sm:p-8 flex flex-col space-y-6">
                    <div class="text-4xl md:text-5xl font-heading font-medium text-blue select-none">{{ sprintf('%02d', $i + 1) }}</div>
                    <div>
                        <h3 class="text-lg font-heading font-medium text-ink mb-2">{{ $title }}</h3>
                        <p class="text-ink text-sm leading-relaxed">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 4. CTA -->
<section class="max-w-[1440px] mx-auto px-4 md:px-8 pt-4">
    <div class="rounded-[28px] bg-navy text-white p-8 sm:p-14 md:p-20 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:items-end">
        <div class="lg:col-span-7 space-y-5">
            <span class="text-xs uppercase tracking-[0.12em] text-ice font-medium block">SIAP MEMBANTU</span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-white leading-[1.1] tracking-tight">
                Ada Keluhan AC? Ceritakan ke Kami.
            </h2>
            <p class="text-ice text-base leading-relaxed max-w-lg">
                Pesan langsung lewat website, atau tanya dulu lewat WhatsApp jika belum yakin layanan mana yang dibutuhkan.
            </p>
        </div>

        <div class="lg:col-span-5 space-y-5">
            <div class="border-t border-white/20 pt-5 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="block text-xs uppercase tracking-wider text-ice mb-1">WhatsApp</span>
                    <a href="{{ \App\Support\WhatsApp::adminLink() }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-white hover:underline underline-offset-4">{{ \App\Support\WhatsApp::display(\App\Support\WhatsApp::adminNumber()) }}</a>
                </div>
                <div>
                    <span class="block text-xs uppercase tracking-wider text-ice mb-1">Jam Operasional</span>
                    <span class="font-semibold text-white">{{ config('usaha.jam_operasional') }}</span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <x-button href="{{ route('order.create') }}" variant="white" size="lg" class="w-full sm:w-auto">
                    PESAN SEKARANG
                </x-button>
                <x-button href="{{ \App\Support\WhatsApp::adminLink() }}" variant="outline-white" size="lg" class="w-full sm:w-auto" target="_blank" rel="noopener noreferrer">
                    CHAT WHATSAPP
                </x-button>
            </div>
        </div>
    </div>
</section>
@endsection
