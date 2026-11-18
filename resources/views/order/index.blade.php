@extends('layouts.app')

@section('title', 'Pesan Layanan AC')
@section('description', 'Pilih kategori dan jenis pekerjaan AC, lihat total harga otomatis, lalu jadwalkan kunjungan teknisi '.config('usaha.nama').'.')

@section('content')
@php
    // Input lama hanya dipakai jika berupa teks (bukan array kiriman yang dimanipulasi).
    $oldText = fn (string $key, string $default = '') => is_string($value = old($key)) ? $value : $default;
@endphp
<div
    class="pt-10 pb-28 md:pt-16 lg:pb-16 bg-paper min-h-screen"
    x-data="{
        katalog: @js($katalog),
        selectedItems: {},
        name: @js($oldText('name')),
        phone: @js($oldText('phone')),
        address: @js($oldText('address')),
        schedule_date: @js($oldText('schedule_date', now()->addDay()->toDateString())),
        notes: @js($oldText('notes')),
        sesi: @js($oldText('sesi')),
        availability: @js($availability),

        sessionState(key) {
            return (this.availability[this.schedule_date] || {})[key] || { available: false, reason: 'Tidak tersedia' };
        },

        resetSesiIfUnavailable() {
            if (this.sesi && !this.sessionState(this.sesi).available) {
                this.sesi = '';
            }
        },

        cariPekerjaan(id) {
            for (const kategori of this.katalog) {
                const pekerjaan = kategori.pekerjaan.find(p => p.id == id);
                if (pekerjaan) {
                    return { kategori, pekerjaan };
                }
            }
            return null;
        },

        simpanItem(kategori, pekerjaan, qty) {
            qty = Math.min(20, Math.max(1, parseInt(qty) || 1));
            const existing = this.selectedItems[pekerjaan.id];

            this.selectedItems[pekerjaan.id] = {
                id: pekerjaan.id,
                kategoriId: kategori.id,
                kategori: kategori.nama,
                name: pekerjaan.nama,
                price: Number(pekerjaan.harga),
                startingPrice: !!pekerjaan.harga_mulai,
                satuan: pekerjaan.satuan,
                qty: existing ? Math.min(20, existing.qty + qty) : qty
            };
        },

        tambah(kategoriId, pekerjaanId) {
            const kategori = this.katalog.find(k => k.id == kategoriId);
            const pekerjaan = kategori ? kategori.pekerjaan.find(p => p.id == pekerjaanId) : null;
            if (kategori && pekerjaan) {
                this.simpanItem(kategori, pekerjaan, 1);
            }
        },

        get hasStartingPrice() {
            return this.selectedList.some(item => item.startingPrice);
        },

        init() {
            @if(is_array(old('items')))
                @js(array_values(array_filter(old('items'), 'is_array'))).forEach(item => {
                    const found = this.cariPekerjaan(item.service_id);
                    if (found) {
                        this.simpanItem(found.kategori, found.pekerjaan, item.qty);
                    }
                });
            @endif
        },

        changeQty(id, delta) {
            if (this.selectedItems[id]) {
                const newQty = this.selectedItems[id].qty + delta;
                if (newQty <= 0) {
                    delete this.selectedItems[id];
                } else if (newQty <= 20) {
                    this.selectedItems[id].qty = newQty;
                }
            }
        },

        removeItem(id) {
            delete this.selectedItems[id];
        },

        get selectedList() {
            return Object.values(this.selectedItems);
        },

        get subtotal() {
            return this.selectedList.reduce((acc, curr) => acc + (curr.price * curr.qty), 0);
        },

        formatRupiah(amount) {
            return 'Rp ' + Number(amount).toLocaleString('id-ID');
        },

        canSubmit() {
            return this.selectedList.length > 0 &&
                   this.name.trim().length > 0 &&
                   this.phone.trim().length > 0 &&
                   this.address.trim().length > 0 &&
                   this.schedule_date.trim().length > 0 &&
                   this.sesi !== '' &&
                   this.sessionState(this.sesi).available;
        }
    }"
>
    <div class="max-w-[1440px] mx-auto px-4 md:px-8">
        <!-- Page Header -->
        <div class="max-w-2xl mb-10 md:mb-14">
            <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-2">PESAN LAYANAN</span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-heading font-medium text-ink leading-[1.1] tracking-tight">
                Pilih Layanan & Jadwalkan Kunjungan.
            </h1>
            <p class="text-ink-soft text-sm sm:text-base leading-relaxed mt-3">
                Pilih jenis pekerjaan di setiap kategori di bawah ini, lalu isi data diri dan tanggal kunjungan yang Anda kehendaki.
            </p>
        </div>

        <form method="POST" action="{{ route('order.store') }}" id="order-form">
            @csrf

            <!-- Hidden input array for server calculation -->
            <template x-for="(item, index) in selectedList" :key="item.id">
                <div>
                    <input type="hidden" :name="'items[' + index + '][kategori_id]'" :value="item.kategoriId">
                    <input type="hidden" :name="'items[' + index + '][service_id]'" :value="item.id">
                    <input type="hidden" :name="'items[' + index + '][qty]'" :value="item.qty">
                </div>
            </template>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <!-- Left Column: Service Selector & Customer Details -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Step 1: Select Services -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between border-b border-line pb-4">
                            <div>
                                <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">TAHAP 01</span>
                                <h2 class="text-xl font-heading font-medium text-ink">Pilih Layanan AC</h2>
                            </div>
                            <span class="text-xs uppercase tracking-wider font-semibold text-ink" x-text="selectedList.length + ' Dipilih'"></span>
                        </div>

                        <!-- Validation Error for Items -->
                        @if ($errors->has('items') || $errors->has('items.*'))
                            <p class="text-sm text-danger flex items-center gap-1.5 p-3 rounded-xl bg-danger/5 border border-danger/20">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ $errors->first('items') ?: $errors->first('items.*') }}
                            </p>
                        @endif

                        @if ($katalog->isEmpty())
                            <div class="p-5 rounded-2xl border border-line bg-paper text-sm text-ink leading-relaxed">
                                Belum ada layanan yang bisa dipesan online. Silakan hubungi WhatsApp
                                <a href="{{ \App\Support\WhatsApp::adminLink() }}" target="_blank" rel="noopener noreferrer" class="font-semibold underline underline-offset-4">{{ \App\Support\WhatsApp::display(\App\Support\WhatsApp::adminNumber()) }}</a>.
                            </div>
                        @else
                            <!-- Semua jenis pekerjaan, langsung dikelompokkan per kategori -->
                            <div class="space-y-8">
                                @foreach ($katalog as $kategori)
                                    <section class="space-y-3" aria-labelledby="kategori-{{ $kategori['id'] }}">
                                        <div>
                                            <h3 id="kategori-{{ $kategori['id'] }}" class="text-xs uppercase tracking-[0.12em] text-ink font-semibold">{{ $kategori['nama'] }}</h3>
                                            @if ($kategori['catatan_harga'])
                                                <p class="text-xs text-ink-mute leading-relaxed mt-1">{{ $kategori['catatan_harga'] }}</p>
                                            @endif
                                        </div>

                                        @foreach ($kategori['pekerjaan'] as $pekerjaan)
                                            <div
                                                class="p-4 sm:p-5 rounded-2xl border bg-paper transition duration-150"
                                                :class="selectedItems[{{ $pekerjaan['id'] }}] ? 'border-[1.5px] border-blue' : 'border-line hover:border-ink'"
                                            >
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                    <div class="space-y-0.5 flex-1">
                                                        <h4 class="text-base font-heading font-medium text-ink">{{ $pekerjaan['nama'] }}</h4>
                                                        <p class="text-sm text-ink">{{ $pekerjaan['label_harga'] }}</p>
                                                    </div>

                                                    <div class="shrink-0 flex items-center justify-end">
                                                        <template x-if="!selectedItems[{{ $pekerjaan['id'] }}]">
                                                            <button
                                                                type="button"
                                                                @click="tambah({{ $kategori['id'] }}, {{ $pekerjaan['id'] }})"
                                                                class="px-5 py-2 rounded-full text-xs uppercase tracking-wider font-semibold border border-ink text-ink hover:bg-ink hover:text-paper transition cursor-pointer min-h-[44px]"
                                                            >
                                                                + Tambah
                                                            </button>
                                                        </template>

                                                        <template x-if="selectedItems[{{ $pekerjaan['id'] }}]">
                                                            <div class="flex items-center gap-2 bg-mist p-1 rounded-full border border-line">
                                                                <button type="button" @click="changeQty({{ $pekerjaan['id'] }}, -1)" class="w-9 h-9 rounded-full bg-paper hover:bg-fog text-ink font-medium flex items-center justify-center transition cursor-pointer text-sm" aria-label="Kurangi jumlah {{ $pekerjaan['nama'] }}">&minus;</button>
                                                                <span class="min-w-14 text-center text-xs font-semibold text-ink" x-text="selectedItems[{{ $pekerjaan['id'] }}].qty + ' {{ $pekerjaan['satuan'] }}'"></span>
                                                                <button type="button" @click="changeQty({{ $pekerjaan['id'] }}, 1)" class="w-9 h-9 rounded-full bg-ink hover:bg-blue text-paper font-medium flex items-center justify-center transition cursor-pointer text-sm" aria-label="Tambah jumlah {{ $pekerjaan['nama'] }}">+</button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </section>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Step 2: Customer Details -->
                    <div class="pt-6 border-t border-line space-y-6">
                        <div class="border-b border-line pb-4">
                            <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">TAHAP 02</span>
                            <h2 class="text-xl font-heading font-medium text-ink">Data Pemesan & Jadwal Kunjungan</h2>
                            <p class="text-xs text-ink-mute mt-1">Lengkapi informasi kontak dan alamat agar teknisi kami dapat tiba tepat waktu.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-1">
                                <x-input
                                    label="Nama Lengkap"
                                    name="name"
                                    placeholder="Contoh: Hendra Wijaya"
                                    required
                                    x-model="name"
                                />
                            </div>

                            <div class="sm:col-span-1">
                                <x-input
                                    label="Nomor WhatsApp / Telepon"
                                    name="phone"
                                    type="tel"
                                    inputmode="numeric"
                                    placeholder="Contoh: 081234567890"
                                    required
                                    hint="Teknisi akan mengonfirmasi keberangkatan ke nomor ini."
                                    x-model="phone"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <x-textarea
                                    label="Alamat Lengkap Kunjungan"
                                    name="address"
                                    rows="3"
                                    placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, patokan lokasi..."
                                    required
                                    x-model="address"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input
                                    label="Tanggal Kunjungan Teknisi"
                                    name="schedule_date"
                                    type="date"
                                    min="{{ date('Y-m-d') }}"
                                    max="{{ $maxDate }}"
                                    required
                                    hint="Bisa dipilih mulai hari ini sampai {{ \Illuminate\Support\Carbon::parse($maxDate)->translatedFormat('d F Y') }}."
                                    x-model="schedule_date"
                                    x-on:change="resetSesiIfUnavailable()"
                                />
                            </div>

                            <!-- Pilihan sesi kunjungan -->
                            <fieldset class="sm:col-span-2">
                                <legend class="block text-xs uppercase tracking-[0.12em] text-ink-mute font-medium mb-2">
                                    Sesi Kunjungan <span class="text-danger font-semibold">*</span>
                                </legend>
                                <input type="hidden" name="sesi" :value="sesi">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($sessions as $key => $session)
                                        <button
                                            type="button"
                                            @click="if (sessionState('{{ $key }}').available) sesi = '{{ $key }}'"
                                            :disabled="!sessionState('{{ $key }}').available"
                                            :aria-pressed="(sesi === '{{ $key }}').toString()"
                                            class="text-left rounded-xl border p-4 transition min-h-[64px] disabled:cursor-not-allowed disabled:bg-ice disabled:border-line"
                                            :class="sesi === '{{ $key }}' ? 'border-blue ring-1 ring-blue bg-paper' : 'border-line bg-paper hover:border-ink'"
                                        >
                                            <span class="flex items-center justify-between gap-3">
                                                <span>
                                                    <span class="block text-base font-semibold text-ink">{{ $session['label'] }}</span>
                                                    <span class="block text-sm text-ink-mute">{{ $session['jam'] }} WIB</span>
                                                </span>
                                                <span
                                                    x-show="!sessionState('{{ $key }}').available"
                                                    x-text="sessionState('{{ $key }}').reason"
                                                    class="px-2.5 py-1 rounded-full bg-paper border border-line text-[11px] uppercase tracking-wider font-semibold text-ink"
                                                ></span>
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                                <p class="mt-1.5 text-xs text-ink-mute">
                                    Sesi Pagi tidak bisa dipesan untuk hari ini. Sesi Siang hari ini hanya bisa dipesan sebelum pukul 10.00.
                                </p>
                                @error('sesi')
                                    <p class="mt-1.5 text-[13px] text-danger font-medium">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <div class="sm:col-span-2">
                                <x-textarea
                                    label="Catatan Tambahan (Opsional)"
                                    name="notes"
                                    rows="2"
                                    placeholder="Contoh: AC merek Daikin 1 PK di kamar lantai 2, keluhan tidak dingin & berisik."
                                    x-model="notes"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sticky Summary Card (mist) -->
                <div class="lg:col-span-4 lg:sticky lg:top-28 lg:self-start space-y-4">
                    <div class="bg-mist rounded-2xl p-6 sm:p-8 space-y-6">
                        <div class="flex items-center justify-between border-b border-line pb-3">
                            <h3 class="text-base font-heading font-medium text-ink">Ringkasan Pesanan</h3>
                            <span class="text-xs uppercase tracking-wider text-ink-mute" x-text="selectedList.length + ' item'"></span>
                        </div>

                        <!-- Empty state -->
                        <template x-if="selectedList.length === 0">
                            <div class="text-center py-6 text-ink-mute space-y-1">
                                <p class="text-xs font-medium">Belum ada layanan yang dipilih.</p>
                                <p class="text-[11px] text-ink-mute">Klik "+ Tambah" pada layanan yang Anda butuhkan.</p>
                            </div>
                        </template>

                        <!-- Selected Items List -->
                        <template x-if="selectedList.length > 0">
                            <div class="space-y-3 divide-y divide-line/70 max-h-60 overflow-y-auto pr-1">
                                <template x-for="item in selectedList" :key="item.id">
                                    <div class="pt-2.5 first:pt-0 flex items-start justify-between gap-3 text-xs">
                                        <div class="flex-1">
                                            <div class="font-medium text-ink" x-text="item.kategori + ' – ' + item.name"></div>
                                            <div class="text-ink-mute text-[11px]">
                                                <span x-text="item.qty + ' ' + item.satuan + ' × ' + (item.startingPrice ? 'mulai ' : '') + formatRupiah(item.price)"></span>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <div class="font-medium text-ink" x-text="formatRupiah(item.price * item.qty)"></div>
                                            <button
                                                type="button"
                                                @click="removeItem(item.id)"
                                                class="text-danger hover:underline text-[10px] cursor-pointer"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Subtotal & System Notice -->
                        <div class="border-t border-line pt-4 space-y-2 text-xs">
                            <div class="flex items-center justify-between text-ink-soft">
                                <span>Subtotal Layanan:</span>
                                <span class="font-medium text-ink" x-text="formatRupiah(subtotal)"></span>
                            </div>

                            <div class="flex items-center justify-between text-ink-mute">
                                <span>Kode Unik Verifikasi:</span>
                                <span class="text-[11px]">Dihitung server (+1 s/d 999)</span>
                            </div>

                            <div class="border-t border-line pt-4 flex items-baseline justify-between">
                                <span class="text-xs uppercase tracking-wider text-ink-mute font-medium">TOTAL ESTIMASI:</span>
                                <span class="text-3xl font-heading font-medium text-ink" x-text="formatRupiah(subtotal)"></span>
                            </div>

                            <p class="text-[11px] text-ink-mute leading-relaxed pt-2">
                                Total akhir dihitung ulang oleh sistem saat pesanan dibuat.
                            </p>
                            <p x-show="hasStartingPrice" x-cloak class="text-[11px] text-ink leading-relaxed bg-paper border border-line rounded-lg p-3">
                                Layanan bertanda "Mulai dari" biayanya bisa bertambah di lokasi sesuai kondisi pemasangan atau tingkat kerusakan. Teknisi akan mengonfirmasi sebelum pekerjaan dimulai.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <x-button
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="w-full"
                                ::disabled="!canSubmit()"
                            >
                                LANJUT KE PEMBAYARAN
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Mobile Sticky Bottom Summary Bar -->
    <div
        class="lg:hidden fixed bottom-0 inset-x-0 bg-paper border-t border-line p-4 z-40"
        x-show="selectedList.length > 0"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
    >
        <div class="max-w-[1440px] mx-auto flex items-center justify-between gap-4">
            <div>
                <span class="text-[11px] text-ink-mute uppercase tracking-wider block">Total Estimasi</span>
                <span class="text-xl font-heading font-medium text-ink leading-tight block" x-text="formatRupiah(subtotal)"></span>
            </div>

            <x-button
                type="submit"
                form="order-form"
                variant="primary"
                size="md"
                ::disabled="!canSubmit()"
            >
                LANJUT BAYAR
            </x-button>
        </div>
    </div>
</div>
@endsection
