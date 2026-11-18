@extends('layouts.app')

@section('title', 'Pembayaran Pesanan')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
@php
    $allMethods = $paymentGroups->flatMap(fn ($g) => $g['methods']);
    $initialMethod = old('payment_method');
    if (! $allMethods->contains('key', $initialMethod)) {
        $initialMethod = $allMethods->first()['key'] ?? null;
    }
    $initialTab = $allMethods->firstWhere('key', $initialMethod)['type'] ?? null;
    $methodNames = $allMethods->pluck('name', 'key');
@endphp
<div class="py-10 md:py-16 bg-paper min-h-screen" x-data="{
    copied: false,
    copiedKey: null,
    tab: @js($initialTab),
    method: @js($initialMethod),
    methodNames: @js($methodNames),
    copyText(text, key) {
        navigator.clipboard.writeText(text);
        this.copiedKey = key;
        setTimeout(() => this.copiedKey = null, 2500);
    },
    filePreview: null,
    fileName: null,
    copyAmount() {
        navigator.clipboard.writeText('{{ $order->total }}');
        this.copied = true;
        setTimeout(() => this.copied = false, 2500);
    },
    handleFile(e) {
        const file = e.target.files[0];
        if (file) {
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (event) => {
                this.filePreview = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}">
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 space-y-10">
        <!-- Page Header & Order Code -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 border-b border-line pb-6">
            <div>
                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-2">
                    KODE PESANAN &bull; {{ $order->code }}
                </span>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-ink leading-[1.1] tracking-tight">
                    Selesaikan Pembayaran.
                </h1>
                <p class="text-ink-soft text-sm mt-2">Dibuat pada {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                <x-status-badge :status="$order->status" size="lg" />
                <a href="{{ route('order.status', ['code' => $order->code]) }}" class="text-xs uppercase tracking-wider font-semibold text-ink hover:underline underline-offset-4">
                    LIHAT STATUS ↗
                </a>
            </div>
        </div>

        @if ($order->status->value === 'dibatalkan')
            <x-alert type="danger" :dismissible="false">
                <strong>Pesanan Dibatalkan.</strong>
                {{ $order->alasan_batal ?? 'Pesanan ini sudah dibatalkan.' }}
                <div class="mt-2 text-xs">Pembayaran untuk pesanan ini tidak dapat dilanjutkan. Silakan buat pesanan baru atau hubungi admin via WhatsApp.</div>
            </x-alert>
        @endif

        @if ($order->status->value === 'ditolak')
            <x-alert type="danger">
                <strong>Pembayaran Ditolak:</strong>
                {{ $order->latestPayment?->note ?? 'Bukti pembayaran tidak sesuai atau tidak terbaca.' }}
                <div class="mt-2 text-xs">Silakan periksa kembali transfer Anda dan unggah ulang bukti yang benar di formulir bawah ini.</div>
                @if ($order->latestPayment?->verified_at)
                    <div class="mt-1 text-xs font-medium">
                        Batas unggah ulang: {{ $order->latestPayment->verified_at->copy()->addHours((int) config('booking.kedaluwarsa_jam', 24))->translatedFormat('d F Y, H:i') }} WIB. Setelah itu pesanan dibatalkan otomatis.
                    </div>
                @endif
            </x-alert>
        @endif

        @if ($order->status->value === 'menunggu_pembayaran')
            <div class="bg-paper border border-line rounded-2xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-heading font-medium text-ink">Ingin memastikan jadwal teknisi dulu?</h2>
                    <p class="text-sm text-ink mt-1">Kirim detail pesanan ke admin lewat WhatsApp. Pesannya sudah kami siapkan.</p>
                </div>
                <x-button href="{{ \App\Support\WhatsApp::adminLink(\App\Support\OrderMessages::customerNewOrder($order)) }}" target="_blank" rel="noopener noreferrer" variant="secondary" size="md" class="shrink-0">
                    KIRIM KE ADMIN
                </x-button>
            </div>
        @endif

        @if ($order->status->value === 'menunggu_verifikasi')
            <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-warning"></span>
                    <h2 class="text-lg font-heading font-medium text-ink">Bukti Pembayaran Sedang Diverifikasi</h2>
                </div>
                <p class="text-ink-soft text-sm leading-relaxed max-w-2xl">
                    Bukti pembayaran Anda telah diterima sistem dengan aman. Admin POOJA COOL sedang mencocokkan mutasi rekening dan akan segera memperbarui status penugasan teknisi.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <x-button
                        href="{{ \App\Support\WhatsApp::adminLink(\App\Support\OrderMessages::customerPaid($order)) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        variant="primary"
                        size="md"
                    >
                        KONFIRMASI VIA WHATSAPP
                    </x-button>
                    <x-button href="{{ route('order.status', ['code' => $order->code]) }}" variant="secondary" size="md">
                        PANTAU STATUS PESANAN
                    </x-button>
                </div>
            </div>
        @elseif ($order->status->value === 'disetujui' || $order->status->value === 'selesai')
            <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
                    <h2 class="text-lg font-heading font-medium text-ink">Pembayaran Telah Disetujui</h2>
                </div>
                <p class="text-ink-soft text-sm leading-relaxed max-w-2xl">
                    Jadwal kunjungan teknisi telah dikonfirmasi untuk tanggal <strong>{{ $order->schedule_date->translatedFormat('d F Y') }}{{ $order->sesiLabel() ? ', sesi '.$order->sesiLabel() : '' }}</strong>. Teknisi kami akan menghubungi nomor {{ mask_phone($order->phone) }} sebelum menuju lokasi Anda.
                </p>
                <div class="pt-2">
                    <x-button href="{{ route('order.status', ['code' => $order->code]) }}" variant="primary" size="md">
                        LIHAT RINCIAN PENUGASAN
                    </x-button>
                </div>
            </div>
        @endif

        <!-- Summary & Total Amount Card (mist) -->
        <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-6">
            <h2 class="text-base font-heading font-medium text-ink border-b border-line pb-3">Rincian Pembayaran</h2>

            <!-- Items Table -->
            <div class="divide-y divide-line/70 text-sm">
                @foreach ($order->items as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-medium text-ink">{{ $item->service_name }}</span>
                            <span class="text-xs text-ink-mute block">{{ $item->qty }} {{ $item->service?->satuan ?? 'unit' }} &times; {{ $item->service?->harga_mulai ? 'Mulai dari ' : '' }}{{ format_rupiah($item->price_at_order) }}</span>
                        </div>
                        <span class="font-medium text-ink">{{ format_rupiah($item->subtotal) }}</span>
                    </div>
                @endforeach

                @php
                    $itemHargaMulai = $order->items->filter(fn ($item) => $item->service?->harga_mulai);
                    $catatanKategori = $itemHargaMulai->map(fn ($item) => $item->service?->kategori?->catatan_harga)->filter()->unique();
                @endphp
                @if ($itemHargaMulai->isNotEmpty())
                    <div class="py-3 text-xs text-ink leading-relaxed space-y-1.5">
                        <p>Layanan bertanda "Mulai dari" biayanya bisa bertambah di lokasi sesuai kondisi pemasangan atau tingkat kerusakan. Teknisi akan mengonfirmasi sebelum pekerjaan dimulai.</p>
                        @foreach ($catatanKategori as $catatan)
                            <p class="text-ink-mute">{{ $catatan }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="py-3 flex items-center justify-between text-ink-soft text-xs">
                    <span>Subtotal Layanan:</span>
                    <span class="font-medium text-ink">{{ format_rupiah($order->subtotal) }}</span>
                </div>

                <div class="py-3 flex items-center justify-between text-ink-soft text-xs">
                    <div class="flex items-center gap-1.5">
                        <span>Kode Unik Verifikasi:</span>
                        <span class="px-2 py-0.5 rounded-full bg-paper text-ink text-[11px] font-semibold border border-line">+{{ $order->unique_code }}</span>
                    </div>
                    <span class="font-medium text-ink">+{{ format_rupiah($order->unique_code) }}</span>
                </div>
            </div>

            <!-- Big Highlight Total Box -->
            <div class="p-6 rounded-xl bg-paper border border-line flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">TOTAL YANG HARUS DIBAYAR</span>
                    <span class="text-3xl sm:text-4xl font-heading font-medium text-ink tracking-tight block mt-1">
                        {{ format_rupiah($order->total) }}
                    </span>
                    <span class="text-xs text-danger font-medium block mt-1">
                        Wajib transfer sesuai nominal tepat termasuk 3 digit kode unik agar diverifikasi cepat.
                    </span>
                </div>

                <div class="shrink-0">
                    <button
                        type="button"
                        @click="copyAmount()"
                        class="px-5 py-3 rounded-full border border-ink bg-paper text-ink hover:bg-mist text-xs uppercase tracking-wider font-semibold transition cursor-pointer min-h-[44px]"
                    >
                        <template x-if="!copied">
                            <span>SALIN NOMINAL PAS ↗</span>
                        </template>
                        <template x-if="copied">
                            <span class="text-success font-semibold">NOMINAL TERSALIN!</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>

        @if ($order->status->value !== 'dibatalkan')
        <!-- Two Columns: Metode Pembayaran (Left) + Upload Box (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            <!-- Left: Pilih metode pembayaran -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-6">
                    <div>
                        <span class="text-xs uppercase tracking-[0.12em] text-ink font-medium block mb-1">LANGKAH 1</span>
                        <h3 class="text-xl font-heading font-medium text-ink">Pilih Metode Pembayaran</h3>
                    </div>

                    @if ($paymentGroups->isEmpty())
                        <p class="text-sm text-ink bg-paper border border-line rounded-xl p-4">
                            Metode pembayaran belum tersedia. Silakan hubungi admin melalui WhatsApp.
                        </p>
                    @else
                        <!-- Tab jenis metode -->
                        <div class="flex gap-1 p-1 bg-paper rounded-full border border-line" role="tablist" aria-label="Jenis metode pembayaran">
                            @foreach ($paymentGroups as $type => $group)
                                <button
                                    type="button"
                                    role="tab"
                                    @click="tab = '{{ $type }}'; method = '{{ $group['methods']->first()['key'] }}'"
                                    :aria-selected="(tab === '{{ $type }}').toString()"
                                    :class="tab === '{{ $type }}' ? 'bg-ink text-paper' : 'text-ink hover:bg-ice'"
                                    class="flex-1 px-3 py-2 rounded-full text-[11px] sm:text-xs uppercase tracking-wider font-semibold transition cursor-pointer min-h-[40px]"
                                >
                                    {{ $group['label'] }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Daftar metode per tab -->
                        @foreach ($paymentGroups as $type => $group)
                            <div x-show="tab === '{{ $type }}'" x-cloak class="space-y-3" role="radiogroup" aria-label="{{ $group['label'] }}">
                                @foreach ($group['methods'] as $pm)
                                    <label
                                        class="block bg-paper rounded-2xl border p-4 sm:p-5 cursor-pointer transition"
                                        :class="method === '{{ $pm['key'] }}' ? 'border-blue ring-1 ring-blue' : 'border-line hover:border-ink'"
                                    >
                                        <input type="radio" value="{{ $pm['key'] }}" x-model="method" class="sr-only">

                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-base font-semibold text-ink">{{ $pm['name'] }}</span>
                                            <span
                                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0"
                                                :class="method === '{{ $pm['key'] }}' ? 'border-blue' : 'border-line'"
                                                aria-hidden="true"
                                            >
                                                <span class="w-2.5 h-2.5 rounded-full bg-blue" x-show="method === '{{ $pm['key'] }}'"></span>
                                            </span>
                                        </div>

                                        @if ($pm['type'] === 'qris')
                                            @php($qrisImage = $pm['image'] && file_exists(public_path($pm['image'])) ? $pm['image'] : null)
                                            <div class="mt-4 text-center space-y-3">
                                                @if ($qrisImage)
                                                    {{-- Nama pemilik QRIS diambil dari config/usaha.php ('qris_nama_pemilik'). --}}
                                                    <a href="{{ asset($qrisImage) }}" target="_blank" rel="noopener" class="block mx-auto max-w-[300px] rounded-xl overflow-hidden border border-line bg-paper">
                                                        <img src="{{ asset($qrisImage) }}" alt="Kode QRIS pembayaran" width="724" height="1024" class="w-full h-auto object-contain" />
                                                    </a>
                                                    <p class="text-sm text-ink">Atas nama: <strong>{{ $pm['account_name'] }}</strong></p>
                                                    <p class="text-xs text-ink-mute">Ketuk gambar untuk memperbesar.</p>
                                                @else
                                                    <div class="mx-auto max-w-[260px] aspect-square border-2 border-dashed border-line rounded-xl flex items-center justify-center p-6">
                                                        <p class="text-sm text-ink">Kode QR belum tersedia. Silakan pilih Transfer Bank atau E-Wallet.</p>
                                                    </div>
                                                @endif
                                                @if ($qrisImage)
                                                    <x-button href="{{ asset($qrisImage) }}" variant="secondary" size="sm" download="qris-pooja-cool.{{ pathinfo($qrisImage, PATHINFO_EXTENSION) }}">
                                                        UNDUH QRIS
                                                    </x-button>
                                                @endif
                                            </div>
                                        @else
                                            <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
                                                <div>
                                                    <span class="block text-[11px] uppercase tracking-wider text-ink-mute mb-0.5">
                                                        {{ $pm['type'] === 'ewallet' ? 'Nomor akun' : 'Nomor rekening' }}
                                                    </span>
                                                    <span class="block text-xl font-semibold text-ink font-mono tracking-wide">{{ $pm['account_number'] }}</span>
                                                    <span class="block text-sm text-ink mt-0.5">a.n. {{ $pm['account_name'] }}</span>
                                                </div>
                                                <button
                                                    type="button"
                                                    @click.prevent="copyText(@js((string) $pm['account_number']), '{{ $pm['key'] }}')"
                                                    class="px-4 py-2 rounded-full border border-ink text-ink text-[11px] uppercase tracking-wider font-semibold hover:bg-ink hover:text-paper transition cursor-pointer min-h-[40px]"
                                                >
                                                    <span x-show="copiedKey !== '{{ $pm['key'] }}'">SALIN NOMOR</span>
                                                    <span x-show="copiedKey === '{{ $pm['key'] }}'" x-cloak>TERSALIN ✓</span>
                                                </button>
                                            </div>
                                        @endif

                                        @if ($pm['note'])
                                            <p class="mt-3 text-xs text-ink-mute leading-relaxed">{{ $pm['note'] }}</p>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    @endif

                    <!-- Langkah singkat -->
                    <ol class="space-y-4 pt-4 border-t border-line">
                        <li class="flex items-start gap-4">
                            <span class="text-2xl font-heading font-medium text-blue leading-none">01</span>
                            <p class="text-sm text-ink leading-relaxed">Pilih metode, lalu bayar ke QRIS, rekening, atau akun e-wallet yang tertera.</p>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="text-2xl font-heading font-medium text-blue leading-none">02</span>
                            <p class="text-sm text-ink leading-relaxed">Bayar tepat <strong>{{ format_rupiah($order->total) }}</strong>, termasuk kode unik, agar cepat diverifikasi.</p>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="text-2xl font-heading font-medium text-blue leading-none">03</span>
                            <p class="text-sm text-ink leading-relaxed">Simpan bukti bayar, lalu unggah di Langkah 2.</p>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Right: Upload Form -->
            <div class="lg:col-span-7 bg-mist rounded-2xl p-6 sm:p-8 space-y-6">
                <div>
                    <span class="text-xs uppercase tracking-[0.12em] text-ink font-medium block mb-1">LANGKAH 2</span>
                    <h3 class="text-xl font-heading font-medium text-ink">Unggah Bukti Transfer</h3>
                    <p class="text-xs text-ink-soft mt-1">
                        Kirimkan foto atau screenshot bukti transfer Anda untuk diverifikasi oleh admin.
                    </p>
                </div>

                @if ($order->canUploadProof())
                    <form method="POST" action="{{ route('payment.upload', ['code' => $order->code]) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="payment_method" :value="method">

                        <!-- Metode yang dipilih -->
                        <div>
                            <div class="flex items-center justify-between gap-3 bg-paper border border-line rounded-xl px-4 py-3">
                                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium">Metode dipilih</span>
                                <span class="text-sm font-semibold text-ink" x-text="methodNames[method] ?? '-'"></span>
                            </div>
                            @error('payment_method')
                                <p class="text-[13px] text-danger font-medium mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dashed Upload Container -->
                        <div>
                            <label class="block text-xs uppercase tracking-[0.12em] text-ink-mute font-medium mb-2">
                                FILE BUKTI PEMBAYARAN <span class="text-danger font-semibold">*</span>
                            </label>

                            <div
                                class="border-2 border-dashed border-line rounded-2xl p-8 text-center bg-paper hover:border-ink transition cursor-pointer"
                                @click="$refs.proofInput.click()"
                            >
                                <input
                                    type="file"
                                    x-ref="proofInput"
                                    name="proof"
                                    id="proof"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                    @change="handleFile($event)"
                                    class="hidden"
                                    required
                                />

                                <!-- No preview -->
                                <template x-if="!filePreview">
                                    <div class="space-y-3">
                                        <!-- Minimal monochrome line icon -->
                                        <svg class="w-8 h-8 text-ink mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-ink">Klik untuk memilih file bukti transfer</p>
                                            <p class="text-xs text-ink-mute mt-1">JPG, JPEG, PNG, atau WEBP (Maksimal 2 MB)</p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Live Preview -->
                                <template x-if="filePreview">
                                    <div class="space-y-3">
                                        <img :src="filePreview" alt="Pratinjau Bukti" class="max-h-56 rounded-xl mx-auto object-contain" />
                                        <div class="text-xs font-semibold text-ink flex items-center justify-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-success"></span>
                                            <span x-text="fileName"></span>
                                        </div>
                                        <p class="text-[11px] text-ink-mute">Klik lagi jika ingin mengganti gambar.</p>
                                    </div>
                                </template>
                            </div>

                            @error('proof')
                                <p class="text-[13px] text-danger font-medium flex items-center gap-1 mt-1.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Action Submit -->
                        <div>
                            <x-button
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-full"
                            >
                                KIRIM BUKTI PEMBAYARAN
                            </x-button>
                        </div>
                    </form>
                @else
                    <div class="p-6 rounded-xl bg-paper border border-line text-center space-y-2">
                        <p class="text-sm font-medium text-ink">
                            Bukti pembayaran pesanan ini telah diterima.
                        </p>
                        <p class="text-xs text-ink-mute">
                            Unggah ulang hanya diizinkan apabila pembayaran ditolak oleh admin.
                        </p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
