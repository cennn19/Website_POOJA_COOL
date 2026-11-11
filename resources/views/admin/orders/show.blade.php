@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{
    proofModalOpen: false,
    rejectModalOpen: false,
    proofUrl: '{{ $order->latestPayment ? route('admin.payments.proof', $order->latestPayment) : '' }}'
}">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-neutral-600 hover:text-neutral-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Kembali ke Daftar Pesanan</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-neutral-500">Status Saat Ini:</span>
            <x-status-badge :status="$order->status" size="lg" />
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Customer Details & Ordered Items -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Order Header Card -->
            <div class="bg-white rounded-3xl p-6 border border-neutral-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Kode Transaksi:</span>
                        <h1 class="text-2xl font-black font-mono text-primary-900 tracking-wider mt-0.5">{{ $order->code }}</h1>
                    </div>
                    <div class="text-right text-xs text-neutral-500">
                        <div>Dipesan: {{ $order->created_at->translatedFormat('d F Y') }}</div>
                        <div class="text-[11px]">{{ $order->created_at->format('H:i') }} WIB</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-neutral-500 block">Nama Pemesan:</span>
                        <span class="text-sm font-bold text-neutral-900 block mt-0.5">{{ $order->name }}</span>
                    </div>

                    <div>
                        <span class="text-neutral-500 block">Nomor WhatsApp / Telp:</span>
                        <a
                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->phone) }}"
                            target="_blank"
                            class="text-sm font-bold text-primary-600 hover:underline inline-flex items-center gap-1 mt-0.5"
                        >
                            <span>{{ $order->phone }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>

                    <div>
                        <span class="text-neutral-500 block">Tanggal Kunjungan Teknisi:</span>
                        <span class="text-sm font-bold text-neutral-900 block mt-0.5">{{ $order->schedule_date->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <div>
                        <span class="text-neutral-500 block">Alamat Kunjungan:</span>
                        <span class="text-sm font-medium text-neutral-800 block mt-0.5 leading-relaxed">{{ $order->address }}</span>
                    </div>

                    @if ($order->notes)
                        <div class="sm:col-span-2 pt-2 border-t border-neutral-100">
                            <span class="text-neutral-500 block">Catatan Tambahan Pelanggan:</span>
                            <p class="text-sm text-neutral-800 mt-0.5 italic bg-neutral-50 p-3 rounded-xl border border-neutral-200/60 leading-relaxed">
                                "{{ $order->notes }}"
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ordered Services Snapshot -->
            <div class="bg-white rounded-3xl p-6 border border-neutral-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h2 class="text-base font-bold text-neutral-900">Rincian Layanan (Snapshot Saat Pemesanan)</h2>
                    <span class="text-xs text-neutral-500">{{ $order->items->count() }} item</span>
                </div>

                <div class="divide-y divide-neutral-100 text-sm">
                    @foreach ($order->items as $item)
                        <div class="py-3 flex items-center justify-between">
                            <div class="space-y-0.5">
                                <div class="font-bold text-neutral-900">{{ $item->service_name }}</div>
                                <div class="text-xs text-neutral-500">{{ $item->qty }} unit x {{ format_rupiah($item->price_at_order) }}</div>
                            </div>
                            <div class="font-bold text-neutral-900">
                                {{ format_rupiah($item->subtotal) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Verification & Proof Image Section -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Expected Amount Card -->
            <div class="bg-white rounded-3xl p-6 border border-neutral-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 uppercase tracking-wider">Pencocokan Mutasi QRIS</h3>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between text-neutral-600">
                        <span>Subtotal Layanan:</span>
                        <span class="font-semibold text-neutral-900">{{ format_rupiah($order->subtotal) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-neutral-600">
                        <span>Kode Unik Verifikasi:</span>
                        <span class="font-bold text-primary-700 bg-primary-100 px-2 py-0.5 rounded-full">+{{ $order->unique_code }}</span>
                    </div>

                    <div class="pt-3 border-t border-neutral-200 flex items-baseline justify-between">
                        <span class="text-xs font-bold text-neutral-700">Nominal Wajib Mutasi:</span>
                        <span class="text-2xl font-black text-primary-900">{{ format_rupiah($order->total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Proof Card -->
            <div class="bg-white rounded-3xl p-6 border border-neutral-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="text-sm font-bold text-neutral-900 uppercase tracking-wider">Bukti Transfer Pelanggan</h3>
                    @if ($order->latestPayment)
                        <span class="text-[11px] text-neutral-500">
                            Diunggah: {{ $order->latestPayment->created_at->diffForHumans() }}
                        </span>
                    @endif
                </div>

                @if ($order->latestPayment)
                    <div class="space-y-4">
                        <!-- Image thumbnail with zoom click -->
                        <div
                            @click="proofModalOpen = true"
                            class="group relative aspect-4/3 rounded-2xl overflow-hidden border-2 border-neutral-200 hover:border-primary-500 cursor-pointer bg-neutral-900 flex items-center justify-center transition shadow-xs"
                        >
                            <img
                                src="{{ route('admin.payments.proof', $order->latestPayment) }}"
                                alt="Bukti Transfer {{ $order->code }}"
                                class="w-full h-full object-contain group-hover:scale-105 transition duration-200"
                            />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                                <span>Klik untuk Perbesar</span>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-neutral-500">Status Pembayaran:</span>
                                <span class="font-bold text-neutral-800">{{ $order->latestPayment->status->label() }}</span>
                            </div>

                            @if ($order->latestPayment->verifier)
                                <div class="flex items-center justify-between">
                                    <span class="text-neutral-500">Diverifikasi Oleh:</span>
                                    <span class="font-medium text-neutral-800">{{ $order->latestPayment->verifier->name }}</span>
                                </div>
                            @endif

                            @if ($order->latestPayment->verified_at)
                                <div class="flex items-center justify-between">
                                    <span class="text-neutral-500">Waktu Verifikasi:</span>
                                    <span class="font-medium text-neutral-800">{{ $order->latestPayment->verified_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                            @endif

                            @if ($order->latestPayment->note)
                                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-danger-600 mt-2">
                                    <strong>Alasan Ditolak Sebelumnya:</strong> {{ $order->latestPayment->note }}
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center bg-neutral-50 rounded-2xl border border-neutral-200 space-y-2">
                        <svg class="w-10 h-10 mx-auto text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-xs text-neutral-500">Pelanggan belum mengunggah gambar bukti transfer.</p>
                    </div>
                @endif
            </div>

            <!-- Admin Actions Card -->
            <div class="bg-white rounded-3xl p-6 border border-neutral-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 uppercase tracking-wider">Aksi Verifikasi</h3>

                @if ($order->status->value === 'menunggu_verifikasi')
                    <div class="space-y-3">
                        <!-- Approve Form -->
                        <form method="POST" action="{{ route('admin.orders.approve', $order) }}" onsubmit="return confirm('Apakah Anda yakin telah mencocokkan mutasi pembayaran dan ingin menyetujui pesanan ini?')">
                            @csrf
                            <button
                                type="submit"
                                class="w-full py-3 px-4 rounded-xl font-bold text-white bg-success-600 hover:bg-green-700 transition duration-150 flex items-center justify-center gap-2 cursor-pointer shadow-sm min-h-[44px]"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Setujui Pembayaran</span>
                            </button>
                        </form>

                        <!-- Reject Trigger Button -->
                        <button
                            type="button"
                            @click="rejectModalOpen = true"
                            class="w-full py-3 px-4 rounded-xl font-bold text-danger-600 border-2 border-danger-600 hover:bg-rose-50 transition duration-150 flex items-center justify-center gap-2 cursor-pointer min-h-[44px]"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Tolak Bukti Transfer</span>
                        </button>
                    </div>
                @elseif ($order->status->value === 'disetujui')
                    <form method="POST" action="{{ route('admin.orders.complete', $order) }}" onsubmit="return confirm('Apakah pekerjaan servis oleh teknisi telah selesai sepenuhnya?')">
                        @csrf
                        <button
                            type="submit"
                            class="w-full py-3.5 px-4 rounded-xl font-bold text-white bg-primary-600 hover:bg-primary-700 transition duration-150 flex items-center justify-center gap-2 cursor-pointer shadow-sm min-h-[44px]"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Tandai Servis Selesai</span>
                        </button>
                    </form>
                @elseif ($order->status->value === 'selesai')
                    <div class="p-4 rounded-xl bg-primary-50 text-primary-800 text-xs text-center font-bold flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Seluruh proses pesanan telah selesai.</span>
                    </div>
                @else
                    <div class="text-xs text-neutral-500 text-center py-2">
                        Menunggu pelanggan melakukan pembayaran dan mengunggah bukti transfer.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal 1: Image Zoom Modal -->
    <div
        x-show="proofModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black/85 backdrop-blur-sm flex items-center justify-center p-4"
        @keydown.escape.window="proofModalOpen = false"
    >
        <div class="relative max-w-4xl w-full bg-neutral-900 rounded-3xl p-4 shadow-2xl space-y-3" @click.outside="proofModalOpen = false">
            <div class="flex items-center justify-between text-white border-b border-neutral-800 pb-3">
                <span class="text-sm font-bold font-mono">Bukti Transfer: {{ $order->code }}</span>
                <button
                    type="button"
                    @click="proofModalOpen = false"
                    class="p-2 rounded-xl text-neutral-400 hover:text-white hover:bg-neutral-800 transition"
                    aria-label="Tutup"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="max-h-[80vh] overflow-auto flex items-center justify-center">
                @if ($order->latestPayment)
                    <img
                        :src="proofUrl"
                        alt="Bukti Transfer Resolusi Penuh"
                        class="max-w-full max-h-[75vh] object-contain rounded-xl"
                    />
                @endif
            </div>
        </div>
    </div>

    <!-- Modal 2: Reject Reason Form Modal -->
    <div
        x-show="rejectModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
        @keydown.escape.window="rejectModalOpen = false"
    >
        <div class="relative max-w-md w-full bg-white rounded-3xl p-6 sm:p-8 shadow-2xl space-y-4" @click.outside="rejectModalOpen = false">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                <h3 class="text-lg font-bold text-neutral-900">Tolak Bukti Pembayaran</h3>
                <button
                    type="button"
                    @click="rejectModalOpen = false"
                    class="p-1 rounded-lg text-neutral-400 hover:text-neutral-700 transition"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p class="text-xs text-neutral-600 leading-relaxed">
                Tuliskan alasan penolakan secara jelas agar pelanggan dapat memperbaiki atau mengunggah bukti transfer yang valid.
            </p>

            <form method="POST" action="{{ route('admin.orders.reject', $order) }}" class="space-y-4">
                @csrf

                <x-textarea
                    label="Alasan Penolakan"
                    name="reason"
                    rows="3"
                    placeholder="Contoh: Nominal transfer kurang, bukti terpotong/buram, atau dana belum masuk mutasi bank..."
                    required
                />

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="rejectModalOpen = false"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-600 hover:bg-neutral-100 transition min-h-[44px]"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-danger-600 hover:bg-red-700 transition shadow-sm min-h-[44px]"
                    >
                        Tolak & Kirim Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
