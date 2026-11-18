@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="space-y-6" x-data="{
    proofModalOpen: false,
    rejectModalOpen: false,
    cancelModalOpen: {{ $errors->has('alasan_batal') ? 'true' : 'false' }},
    rescheduleModalOpen: {{ $errors->hasAny(['jadwal_tanggal', 'jadwal_sesi']) || old('jadwal_tanggal') ? 'true' : 'false' }},
    proofUrl: '{{ $order->latestPayment ? route('admin.payments.proof', $order->latestPayment) : '' }}'
}">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs uppercase tracking-wider font-semibold text-ink-mute hover:text-ink transition">
            <span>&larr;</span>
            <span>Kembali ke Daftar Pesanan</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs uppercase tracking-wider text-ink-mute">Status:</span>
            <x-status-badge :status="$order->status" size="lg" />
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Customer Details & Ordered Items -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Order Header Card -->
            <div class="bg-paper rounded-2xl p-6 border border-line space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-4">
                    <div>
                        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium">KODE TRANSAKSI</span>
                        <h1 class="text-2xl font-mono font-medium text-ink tracking-wider mt-0.5">{{ $order->code }}</h1>
                    </div>
                    <div class="text-right text-xs text-ink-mute">
                        <div>Dipesan: {{ $order->created_at->translatedFormat('d F Y') }}</div>
                        <div class="text-[11px]">{{ $order->created_at->format('H:i') }} WIB</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-ink-mute uppercase tracking-wider block mb-0.5">Nama Pemesan</span>
                        <span class="text-sm font-medium text-ink block">{{ $order->name }}</span>
                    </div>

                    <div>
                        <span class="text-ink-mute uppercase tracking-wider block mb-0.5">WhatsApp / Telp</span>
                        <a
                            href="{{ \App\Support\WhatsApp::link($order->phone) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm font-medium text-ink hover:underline inline-flex items-center gap-1"
                        >
                            <span>{{ $order->phone }}</span>
                            <span>↗</span>
                        </a>
                    </div>

                    <div>
                        <span class="text-ink-mute uppercase tracking-wider block mb-0.5">Tanggal Kunjungan</span>
                        <span class="text-sm font-medium text-ink block">{{ $order->schedule_date->translatedFormat('l, d F Y') }}</span>
                        <span class="text-xs text-ink-mute block">{{ $order->sesiLabel() ? 'Sesi '.$order->sesiLabel() : 'Sesi belum ditentukan' }}</span>
                    </div>

                    <div>
                        <span class="text-ink-mute uppercase tracking-wider block mb-0.5">Alamat Kunjungan</span>
                        <span class="text-sm text-ink block leading-relaxed">{{ $order->address }}</span>
                    </div>

                    @if ($order->notes)
                        <div class="sm:col-span-2 pt-3 border-t border-line">
                            <span class="text-ink-mute uppercase tracking-wider block mb-1">Catatan Tambahan</span>
                            <p class="text-sm text-ink italic bg-mist p-3 rounded-xl leading-relaxed">
                                "{{ $order->notes }}"
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kabari pelanggan via WhatsApp (pesan sesuai status) -->
            @php($customerMessage = \App\Support\OrderMessages::adminToCustomer($order))
            <div class="bg-paper rounded-2xl p-6 border border-line space-y-4" x-data="{ showPreview: false }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">KABARI PELANGGAN</span>
                        <p class="text-sm text-ink mt-1">
                            Pesan untuk status <strong>{{ $order->status->label() }}</strong> sudah disiapkan. Kirim setelah Anda menyetujui, menolak, atau menyelesaikan pesanan.
                        </p>
                    </div>
                    <x-button href="{{ \App\Support\WhatsApp::link($order->phone, $customerMessage) }}" target="_blank" rel="noopener noreferrer" variant="primary" size="md" class="shrink-0">
                        KIRIM VIA WHATSAPP
                    </x-button>
                </div>
                <button type="button" @click="showPreview = !showPreview" class="text-xs uppercase tracking-wider font-semibold text-blue hover:underline underline-offset-4 cursor-pointer">
                    <span x-text="showPreview ? 'Sembunyikan isi pesan' : 'Lihat isi pesan'"></span>
                </button>
                <pre x-show="showPreview" x-cloak class="whitespace-pre-wrap font-sans text-sm text-ink bg-ice rounded-xl p-4 leading-relaxed">{{ $customerMessage }}</pre>
            </div>

            <!-- Ordered Services Snapshot -->
            <div class="bg-paper rounded-2xl p-6 border border-line space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium">LAYANAN DIPESAN (SNAPSHOT)</span>
                    <span class="text-xs text-ink-mute">{{ $order->items->count() }} item</span>
                </div>

                <div class="divide-y divide-line/70 text-sm">
                    @foreach ($order->items as $item)
                        <div class="py-3 flex items-center justify-between">
                            <div class="space-y-0.5">
                                <div class="font-medium text-ink">{{ $item->service_name }}</div>
                                <div class="text-xs text-ink-mute">{{ $item->qty }} {{ $item->service?->satuan ?? 'unit' }} &times; {{ $item->service?->harga_mulai ? 'mulai ' : '' }}{{ format_rupiah($item->price_at_order) }}</div>
                            </div>
                            <div class="font-medium text-ink">
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
            <div class="bg-paper rounded-2xl p-6 border border-line space-y-4">
                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">PENCOCOKAN MUTASI QRIS</span>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between text-ink-soft">
                        <span>Subtotal Layanan:</span>
                        <span class="font-medium text-ink">{{ format_rupiah($order->subtotal) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-ink-soft">
                        <span>Kode Unik Verifikasi:</span>
                        <span class="font-mono font-medium text-ink">+{{ $order->unique_code }}</span>
                    </div>

                    <div class="pt-3 border-t border-line flex items-baseline justify-between">
                        <span class="text-xs uppercase tracking-wider text-ink-mute font-medium">NOMINAL MUTASI WAJIB:</span>
                        <span class="text-3xl font-heading font-medium text-ink">{{ format_rupiah($order->total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Proof Card -->
            <div class="bg-paper rounded-2xl p-6 border border-line space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium">BUKTI TRANSFER PELANGGAN</span>
                    @if ($order->latestPayment)
                        <span class="text-[11px] text-ink-mute">
                            {{ $order->latestPayment->created_at->diffForHumans() }}
                        </span>
                    @endif
                </div>

                @if ($order->latestPayment)
                    <div class="space-y-4">
                        <!-- Image thumbnail with zoom click -->
                        <div
                            @click="proofModalOpen = true"
                            class="group relative aspect-4/3 rounded-xl overflow-hidden border border-line hover:border-ink cursor-pointer bg-ink flex items-center justify-center transition"
                        >
                            <img
                                src="{{ route('admin.payments.proof', $order->latestPayment) }}"
                                alt="Bukti Transfer {{ $order->code }}"
                                class="w-full h-full object-contain group-hover:scale-102 transition duration-200"
                            />
                            <div class="absolute inset-0 bg-navy/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs uppercase tracking-wider font-semibold gap-1.5">
                                <span>Klik untuk Perbesar ↗</span>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs pt-1">
                            <div class="flex items-center justify-between">
                                <span class="text-ink-mute">Metode Pembayaran:</span>
                                <span class="font-semibold text-ink">{{ $order->latestPayment->methodLabel() ?? 'Tidak dicatat' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-ink-mute">Status Pembayaran:</span>
                                <span class="font-medium text-ink">{{ $order->latestPayment->status->label() }}</span>
                            </div>

                            @if ($order->latestPayment->verifier)
                                <div class="flex items-center justify-between">
                                    <span class="text-ink-mute">Diverifikasi Oleh:</span>
                                    <span class="font-medium text-ink">{{ $order->latestPayment->verifier->name }}</span>
                                </div>
                            @endif

                            @if ($order->latestPayment->verified_at)
                                <div class="flex items-center justify-between">
                                    <span class="text-ink-mute">Waktu Verifikasi:</span>
                                    <span class="font-medium text-ink">{{ $order->latestPayment->verified_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                </div>
                            @endif

                            @if ($order->latestPayment->note)
                                <div class="p-3 rounded-xl bg-danger/5 border border-danger/20 text-danger text-xs mt-2">
                                    <strong>Alasan Ditolak Sebelumnya:</strong> {{ $order->latestPayment->note }}
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center bg-mist rounded-xl space-y-1">
                        <p class="text-xs text-ink-mute">Pelanggan belum mengunggah gambar bukti transfer.</p>
                    </div>
                @endif
            </div>

            <!-- Admin Actions Card -->
            <div class="bg-paper rounded-2xl p-6 border border-line space-y-4">
                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">AKSI ADMINISTRASI</span>

                @if ($order->status->value === 'menunggu_verifikasi')
                    <div class="space-y-3">
                        <!-- Approve Form: Black Button -->
                        <form method="POST" action="{{ route('admin.orders.approve', $order) }}" x-data @submit="if (! confirm('Apakah Anda yakin telah mencocokkan mutasi pembayaran dan ingin menyetujui pesanan ini?')) $event.preventDefault()">
                            @csrf
                            <button
                                type="submit"
                                class="w-full py-3 px-5 rounded-full text-xs uppercase tracking-wider font-semibold text-paper bg-ink hover:bg-blue transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer min-h-[44px]"
                            >
                                <span>SETUJUI PEMBAYARAN</span>
                                <span>↗</span>
                            </button>
                        </form>

                        <!-- Reject Trigger Button: Danger Outline -->
                        <button
                            type="button"
                            @click="rejectModalOpen = true"
                            class="w-full py-3 px-5 rounded-full text-xs uppercase tracking-wider font-semibold text-danger border border-danger hover:bg-danger/5 transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer min-h-[44px]"
                        >
                            <span>TOLAK BUKTI PEMBAYARAN</span>
                            <span>&times;</span>
                        </button>
                    </div>
                @elseif ($order->status->value === 'disetujui')
                    <form method="POST" action="{{ route('admin.orders.complete', $order) }}" x-data @submit="if (! confirm('Apakah seluruh pekerjaan servis telah selesai dikerjakan teknisi?')) $event.preventDefault()">
                        @csrf
                        <button
                            type="submit"
                            class="w-full py-3 px-5 rounded-full text-xs uppercase tracking-wider font-semibold text-paper bg-ink hover:bg-blue transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer min-h-[44px]"
                        >
                            <span>TANDAI SERVIS SELESAI</span>
                            <span>✓</span>
                        </button>
                    </form>
                @elseif ($order->status->value === 'selesai')
                    <div class="p-4 rounded-xl bg-mist text-ink text-xs text-center font-medium">
                        Seluruh proses pesanan telah selesai dan dituntaskan oleh teknisi.
                    </div>
                @elseif ($order->status->value === 'dibatalkan')
                    <div class="p-4 rounded-xl bg-line text-ink text-sm space-y-1">
                        <p class="font-semibold">Pesanan dibatalkan{{ $order->dibatalkan_pada ? ' pada '.$order->dibatalkan_pada->translatedFormat('d F Y, H:i').' WIB' : '' }}.</p>
                        <p>Alasan: {{ $order->alasan_batal ?? '-' }}</p>
                    </div>
                @else
                    <div class="text-xs text-ink-mute text-center py-2">
                        Menunggu pelanggan melakukan pembayaran dan mengunggah bukti bayar.
                    </div>
                @endif

                @if ($order->canBeRescheduled())
                    <div class="pt-4 border-t border-line grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="rescheduleModalOpen = true"
                            class="w-full py-3 px-5 rounded-full text-xs uppercase tracking-wider font-semibold text-ink border border-ink hover:bg-ink hover:text-paper transition duration-150 cursor-pointer min-h-[44px]"
                        >
                            UBAH JADWAL
                        </button>
                        <button
                            type="button"
                            @click="cancelModalOpen = true"
                            class="w-full py-3 px-5 rounded-full text-xs uppercase tracking-wider font-semibold text-danger border border-danger hover:bg-danger hover:text-paper transition duration-150 cursor-pointer min-h-[44px]"
                        >
                            BATALKAN PESANAN
                        </button>
                    </div>
                @endif
            </div>

            <!-- Riwayat perubahan -->
            @if ($order->logs->isNotEmpty())
                <div class="bg-paper rounded-2xl p-6 border border-line space-y-4">
                    <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">RIWAYAT PERUBAHAN</span>
                    <ol class="space-y-3">
                        @foreach ($order->logs as $log)
                            <li class="text-sm border-l-2 border-line pl-3">
                                <p class="text-ink">{{ $log->description }}</p>
                                <p class="text-xs text-ink-mute mt-0.5">
                                    {{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB &bull; {{ $log->user?->name ?? ($log->action === 'bukti_diunggah' ? 'Pelanggan' : 'Sistem otomatis') }}
                                </p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal 1: Image Zoom Modal -->
    <div
        x-show="proofModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-navy/85 flex items-center justify-center p-4"
        @keydown.escape.window="proofModalOpen = false"
    >
        <div class="relative max-w-4xl w-full bg-paper rounded-2xl p-6 space-y-4" @click.outside="proofModalOpen = false">
            <div class="flex items-center justify-between border-b border-line pb-3">
                <span class="text-xs uppercase tracking-wider font-mono text-ink">Bukti Transfer: {{ $order->code }}</span>
                <button
                    type="button"
                    @click="proofModalOpen = false"
                    class="p-1 rounded-lg text-ink-mute hover:text-ink transition cursor-pointer"
                    aria-label="Tutup"
                >
                    <span class="text-lg leading-none">&times;</span>
                </button>
            </div>

            <div class="max-h-[80vh] overflow-auto flex items-center justify-center bg-mist rounded-xl p-2">
                @if ($order->latestPayment)
                    <img
                        :src="proofUrl"
                        alt="Bukti Transfer Resolusi Penuh"
                        class="max-w-full max-h-[75vh] object-contain rounded-lg"
                    />
                @endif
            </div>
        </div>
    </div>

    <!-- Modal 2: Reject Reason Form Modal -->
    <div
        x-show="rejectModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-navy/60 flex items-center justify-center p-4"
        @keydown.escape.window="rejectModalOpen = false"
    >
        <div class="relative max-w-md w-full bg-paper rounded-2xl p-6 sm:p-8 space-y-4" @click.outside="rejectModalOpen = false">
            <div class="flex items-center justify-between border-b border-line pb-3">
                <h3 class="text-base font-heading font-medium text-ink">Tolak Bukti Pembayaran</h3>
                <button
                    type="button"
                    @click="rejectModalOpen = false"
                    class="p-1 rounded-lg text-ink-mute hover:text-ink transition cursor-pointer"
                >
                    <span class="text-lg leading-none">&times;</span>
                </button>
            </div>

            <p class="text-xs text-ink-soft leading-relaxed">
                Tuliskan alasan penolakan agar pelanggan dapat memperbaiki atau mengunggah ulang bukti transfer yang sah.
            </p>

            <form method="POST" action="{{ route('admin.orders.reject', $order) }}" class="space-y-4">
                @csrf

                <x-textarea
                    label="Alasan Penolakan"
                    name="reason"
                    rows="3"
                    placeholder="Contoh: Nominal transfer kurang, bukti terpotong/buram, atau dana belum masuk mutasi..."
                    required
                />

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="rejectModalOpen = false"
                        class="px-4 py-2.5 rounded-full text-xs uppercase tracking-wider text-ink-mute hover:text-ink transition min-h-[44px] cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-semibold text-paper bg-danger hover:bg-danger/90 transition min-h-[44px] cursor-pointer"
                    >
                        Tolak & Kirim Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal: Batalkan Pesanan -->
    @if ($order->canBeCancelled())
        <div
            x-show="cancelModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-navy/60 flex items-center justify-center p-4"
            @keydown.escape.window="cancelModalOpen = false"
            role="dialog"
            aria-modal="true"
            aria-labelledby="cancel-title"
        >
            <div class="relative max-w-md w-full bg-paper rounded-2xl p-6 sm:p-8 space-y-4" @click.outside="cancelModalOpen = false">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h3 id="cancel-title" class="text-base font-heading font-medium text-ink">Batalkan Pesanan {{ $order->code }}?</h3>
                    <button type="button" @click="cancelModalOpen = false" class="p-1 rounded-lg text-ink-mute hover:text-ink transition cursor-pointer" aria-label="Tutup">
                        <span class="text-lg leading-none">&times;</span>
                    </button>
                </div>

                <p class="text-sm text-ink leading-relaxed">
                    Pesanan yang dibatalkan tidak bisa diaktifkan kembali. Kode unik dan slot jadwalnya akan dilepas untuk pesanan lain.
                </p>

                <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" class="space-y-4">
                    @csrf
                    <x-textarea
                        label="Alasan Pembatalan"
                        name="alasan_batal"
                        rows="3"
                        placeholder="Contoh: Pelanggan meminta pembatalan, teknisi tidak tersedia di tanggal tersebut, dll."
                        required
                    />

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="cancelModalOpen = false" class="px-4 py-2.5 rounded-full text-xs uppercase tracking-wider text-ink-mute hover:text-ink transition min-h-[44px] cursor-pointer">
                            Kembali
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-semibold text-paper bg-danger hover:bg-danger/90 transition min-h-[44px] cursor-pointer">
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal: Ubah Jadwal -->
    @if ($order->canBeRescheduled())
        <div
            x-show="rescheduleModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-navy/60 flex items-center justify-center p-4"
            @keydown.escape.window="rescheduleModalOpen = false"
            role="dialog"
            aria-modal="true"
            aria-labelledby="reschedule-title"
        >
            <div class="relative max-w-md w-full bg-paper rounded-2xl p-6 sm:p-8 space-y-4" @click.outside="rescheduleModalOpen = false">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h3 id="reschedule-title" class="text-base font-heading font-medium text-ink">Ubah Jadwal Kunjungan</h3>
                    <button type="button" @click="rescheduleModalOpen = false" class="p-1 rounded-lg text-ink-mute hover:text-ink transition cursor-pointer" aria-label="Tutup">
                        <span class="text-lg leading-none">&times;</span>
                    </button>
                </div>

                <p class="text-sm text-ink">
                    Jadwal saat ini: <strong>{{ $order->schedule_date->translatedFormat('d F Y') }}{{ $order->sesiLabel() ? ', '.$order->sesiLabel() : '' }}</strong>
                </p>

                <form method="POST" action="{{ route('admin.orders.reschedule', $order) }}" class="space-y-4">
                    @csrf
                    <x-input
                        label="Tanggal Baru"
                        name="jadwal_tanggal"
                        type="date"
                        min="{{ date('Y-m-d') }}"
                        :value="$order->schedule_date->toDateString()"
                        required
                    />

                    <x-select
                        label="Sesi Baru"
                        name="jadwal_sesi"
                        :options="collect($sessions)->mapWithKeys(fn ($s, $k) => [$k => $s['label'].' ('.$s['jam'].')'])->all()"
                        :selected="$order->sesi"
                        placeholder="Pilih sesi"
                        required
                    />

                    <label class="flex items-start gap-2 text-sm text-ink cursor-pointer">
                        <input type="checkbox" name="paksa" value="1" @checked(old('paksa')) class="mt-0.5 w-4 h-4 rounded border-line text-blue focus:ring-blue">
                        <span>Paksa meski penuh <span class="block text-xs text-ink-mute">Simpan walaupun kuota sesi tersebut sudah terpenuhi.</span></span>
                    </label>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="rescheduleModalOpen = false" class="px-4 py-2.5 rounded-full text-xs uppercase tracking-wider text-ink-mute hover:text-ink transition min-h-[44px] cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-semibold text-paper bg-ink hover:bg-blue transition min-h-[44px] cursor-pointer">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
