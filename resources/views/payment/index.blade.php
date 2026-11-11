@extends('layouts.app')

@section('content')
<div class="py-8 md:py-14 bg-neutral-50 min-h-screen" x-data="{
    copied: false,
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
    <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-8">
        <!-- Top Status Banner -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-neutral-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-neutral-500 font-semibold uppercase tracking-wider">Kode Pesanan:</span>
                    <span class="text-base font-extrabold text-primary-900 tracking-wider font-mono">{{ $order->code }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-neutral-900">Pembayaran Layanan AC</h1>
                <p class="text-xs text-neutral-500">Dibuat pada: {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                <x-status-badge :status="$order->status" size="lg" />
                <x-button href="{{ route('order.status', ['code' => $order->code]) }}" variant="secondary" size="sm">
                    Lihat Timeline
                </x-button>
            </div>
        </div>

        @if ($order->status->value === 'ditolak')
            <x-alert type="danger">
                <strong>Pembayaran Ditolak:</strong>
                {{ $order->latestPayment?->note ?? 'Bukti pembayaran tidak sesuai atau tidak terbaca.' }}
                <div class="mt-2 text-xs">Silakan periksa kembali transfer Anda dan unggah ulang bukti yang benar di formulir bawah ini.</div>
            </x-alert>
        @endif

        @if ($order->status->value === 'menunggu_verifikasi')
            <div class="rounded-3xl bg-amber-50 border border-amber-200 p-6 sm:p-8 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-warning-500 text-white flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="space-y-1 max-w-lg mx-auto">
                    <h2 class="text-xl font-bold text-amber-950">Bukti Pembayaran Sedang Diverifikasi</h2>
                    <p class="text-sm text-amber-900 leading-relaxed">
                        Terima kasih! Bukti transfer Anda telah tersimpan dengan aman di sistem kami. Admin kami sedang mencocokkan mutasi pembayaran dan akan segera mengonfirmasi jadwal teknisi.
                    </p>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <x-button
                        href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin SejukAbadi, saya sudah mengunggah bukti pembayaran untuk pesanan kode: ' . $order->code . ' sebesar Rp ' . number_format($order->total, 0, ',', '.') . '. Mohon diverifikasi.') }}"
                        target="_blank"
                        variant="accent"
                        size="md"
                    >
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                        </svg>
                        Konfirmasi Cepat via WhatsApp
                    </x-button>

                    <x-button href="{{ route('order.status', ['code' => $order->code]) }}" variant="secondary" size="md">
                        Pantau Status Pesanan
                    </x-button>
                </div>
            </div>
        @elseif ($order->status->value === 'disetujui' || $order->status->value === 'selesai')
            <div class="rounded-3xl bg-emerald-50 border border-emerald-200 p-6 sm:p-8 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-success-600 text-white flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="space-y-1 max-w-lg mx-auto">
                    <h2 class="text-xl font-bold text-emerald-950">Pembayaran Terverifikasi & Disetujui</h2>
                    <p class="text-sm text-emerald-900 leading-relaxed">
                        Jadwal kunjungan teknisi telah dikonfirmasi untuk tanggal <strong>{{ $order->schedule_date->translatedFormat('d F Y') }}</strong>. Teknisi kami akan menghubungi nomor {{ $order->phone }} sebelum keberangkatan.
                    </p>
                </div>
                <div class="pt-2">
                    <x-button href="{{ route('order.status', ['code' => $order->code]) }}" variant="primary" size="md">
                        Lihat Rincian Penugasan
                    </x-button>
                </div>
            </div>
        @endif

        <!-- Order Summary & Amount Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-neutral-200 shadow-sm space-y-6">
            <h2 class="text-lg font-bold text-neutral-900 border-b border-neutral-100 pb-3">Rincian Pembayaran</h2>

            <!-- Itemized table -->
            <div class="divide-y divide-neutral-100 text-sm">
                @foreach ($order->items as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-neutral-900">{{ $item->service_name }}</span>
                            <span class="text-xs text-neutral-500 block">{{ $item->qty }} unit x {{ format_rupiah($item->price_at_order) }}</span>
                        </div>
                        <span class="font-semibold text-neutral-800">{{ format_rupiah($item->subtotal) }}</span>
                    </div>
                @endforeach

                <div class="py-3 flex items-center justify-between text-neutral-600">
                    <span>Subtotal Layanan:</span>
                    <span class="font-medium text-neutral-900">{{ format_rupiah($order->subtotal) }}</span>
                </div>

                <div class="py-3 flex items-center justify-between text-neutral-600">
                    <div class="flex items-center gap-1.5">
                        <span>Kode Unik Verifikasi:</span>
                        <span class="px-2 py-0.5 rounded-full bg-primary-100 text-primary-700 text-[11px] font-bold">+{{ $order->unique_code }}</span>
                    </div>
                    <span class="font-medium text-primary-700">+{{ format_rupiah($order->unique_code) }}</span>
                </div>
            </div>

            <!-- Total highlight box -->
            <div class="p-5 rounded-2xl bg-primary-50 border border-primary-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs text-neutral-600 font-medium block">Total yang Harus Dibayar:</span>
                    <span class="text-3xl sm:text-4xl font-black text-primary-900 tracking-tight block">
                        {{ format_rupiah($order->total) }}
                    </span>
                    <span class="text-[11px] text-danger-600 font-semibold block mt-1">
                        * Wajib transfer sesuai nominal tepat (hingga 3 angka terakhir) agar verifikasi otomatis/cepat.
                    </span>
                </div>

                <div class="shrink-0">
                    <button
                        type="button"
                        @click="copyAmount()"
                        class="px-4 py-2.5 rounded-xl border border-primary-600 bg-white text-primary-600 hover:bg-primary-50 text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-xs min-h-[44px]"
                    >
                        <template x-if="!copied">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                <span>Salin Nominal Pas</span>
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="flex items-center gap-1.5 text-success-600 font-bold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Nominal Tersalin!</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </div>

        <!-- QRIS & Upload Section (Only interactive when canUploadProof) -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            <!-- QRIS Card -->
            <div class="md:col-span-5 bg-white rounded-3xl p-6 border border-neutral-200 shadow-sm text-center space-y-4">
                <div class="border-b border-neutral-100 pb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-500">Metode Pembayaran</span>
                    <h3 class="text-base font-bold text-neutral-900 mt-0.5">QRIS Dinamis / Statis</h3>
                </div>

                <!-- QRIS Visual Card -->
                <div class="p-4 bg-white rounded-2xl border-2 border-neutral-300 shadow-sm inline-block max-w-[280px] mx-auto w-full">
                    <!-- QRIS Header Banner -->
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-neutral-200">
                        <span class="text-xs font-black tracking-widest text-neutral-900">QRIS</span>
                        <span class="text-[9px] font-bold text-neutral-500 uppercase">GPN</span>
                    </div>

                    <!-- Clean QR Code Display -->
                    <div class="bg-neutral-900 p-3 rounded-xl mx-auto flex items-center justify-center">
                        <svg class="w-44 h-44 text-white" viewBox="0 0 100 100" fill="currentColor">
                            <!-- SVG QR Pattern -->
                            <rect width="100" height="100" fill="white" />
                            <!-- Corner 1 -->
                            <rect x="5" y="5" width="30" height="30" fill="black" />
                            <rect x="9" y="9" width="22" height="22" fill="white" />
                            <rect x="13" y="13" width="14" height="14" fill="black" />
                            <!-- Corner 2 -->
                            <rect x="65" y="5" width="30" height="30" fill="black" />
                            <rect x="69" y="9" width="22" height="22" fill="white" />
                            <rect x="73" y="13" width="14" height="14" fill="black" />
                            <!-- Corner 3 -->
                            <rect x="5" y="65" width="30" height="30" fill="black" />
                            <rect x="9" y="69" width="22" height="22" fill="white" />
                            <rect x="13" y="73" width="14" height="14" fill="black" />
                            <!-- Decorative Pattern dots -->
                            <rect x="42" y="8" width="6" height="6" fill="black" />
                            <rect x="52" y="14" width="6" height="6" fill="black" />
                            <rect x="44" y="24" width="6" height="6" fill="black" />
                            <rect x="8" y="42" width="6" height="6" fill="black" />
                            <rect x="14" y="52" width="6" height="6" fill="black" />
                            <rect x="24" y="44" width="6" height="6" fill="black" />
                            <rect x="40" y="40" width="20" height="20" fill="black" />
                            <rect x="45" y="45" width="10" height="10" fill="white" />
                            <rect x="48" y="48" width="4" height="4" fill="black" />
                            <rect x="68" y="42" width="6" height="6" fill="black" />
                            <rect x="84" y="48" width="6" height="6" fill="black" />
                            <rect x="72" y="56" width="6" height="6" fill="black" />
                            <rect x="44" y="68" width="6" height="6" fill="black" />
                            <rect x="54" y="76" width="6" height="6" fill="black" />
                            <rect x="42" y="86" width="6" height="6" fill="black" />
                            <rect x="68" y="68" width="8" height="8" fill="black" />
                            <rect x="80" y="74" width="6" height="6" fill="black" />
                            <rect x="74" y="84" width="8" height="8" fill="black" />
                            <rect x="86" y="86" width="6" height="6" fill="black" />
                        </svg>
                    </div>

                    <div class="mt-2 text-center">
                        <div class="font-bold text-[11px] text-neutral-900 tracking-tight">SEJUKABADI HVAC SERVICES</div>
                        <div class="text-[9px] text-neutral-500 font-mono">NMID: ID1020268899201</div>
                    </div>
                </div>

                <div class="text-xs text-neutral-600 space-y-1">
                    <p class="font-semibold text-neutral-800">Mendukung Seluruh Pembayaran:</p>
                    <p class="text-[11px] text-neutral-500">BCA Mobile, Mandiri Livin, BRImo, BNI Mobile, GoPay, OVO, DANA, ShopeePay, LinkAja.</p>
                </div>
            </div>

            <!-- Form Upload Bukti -->
            <div class="md:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-neutral-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-neutral-900">Unggah Bukti Transfer</h3>
                    <p class="text-xs text-neutral-600 mt-1">Setelah melakukan transfer sesuai nominal di atas, foto atau tangkapan layar bukti bayar Anda.</p>
                </div>

                @if ($order->canUploadProof())
                    <form method="POST" action="{{ route('payment.upload', ['code' => $order->code]) }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <!-- Drag and Drop Box -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-neutral-900">
                                File Foto Bukti Transfer <span class="text-danger-600 font-bold">*</span>
                            </label>

                            <div
                                class="relative border-2 border-dashed border-neutral-300 hover:border-primary-500 rounded-2xl p-6 text-center transition bg-neutral-50 cursor-pointer"
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

                                <!-- No Preview Yet -->
                                <template x-if="!filePreview">
                                    <div class="space-y-2">
                                        <div class="w-12 h-12 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center mx-auto">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-neutral-800">Klik untuk memilih file bukti transfer</p>
                                            <p class="text-xs text-neutral-500 mt-0.5">Format: JPG, JPEG, PNG, WEBP (Maksimal 2 MB)</p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Live Preview -->
                                <template x-if="filePreview">
                                    <div class="space-y-3">
                                        <img :src="filePreview" alt="Pratinjau Bukti" class="max-h-48 rounded-xl mx-auto shadow-sm object-contain" />
                                        <div class="flex items-center justify-center gap-2 text-xs font-semibold text-primary-600">
                                            <svg class="w-4 h-4 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span x-text="fileName"></span>
                                        </div>
                                        <p class="text-[11px] text-neutral-400">Klik lagi jika ingin mengganti gambar.</p>
                                    </div>
                                </template>
                            </div>

                            @error('proof')
                                <p class="text-sm text-danger-600 font-medium flex items-center gap-1 mt-1.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full py-3.5 px-4 rounded-xl text-base font-bold text-white bg-primary-600 hover:bg-primary-700 active:bg-primary-800 transition duration-150 flex items-center justify-center gap-2 cursor-pointer shadow-md min-h-[44px]"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span>Kirim Bukti Pembayaran</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-5 rounded-2xl bg-neutral-100 border border-neutral-200 text-center space-y-2">
                        <p class="text-sm font-semibold text-neutral-800">
                            Bukti pembayaran Anda sudah diterima di sistem.
                        </p>
                        <p class="text-xs text-neutral-500">
                            Anda tidak perlu mengunggah ulang kecuali status pesanan ditolak oleh admin.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
