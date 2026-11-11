@extends('layouts.app')

@section('content')
<div
    class="py-8 md:py-14 bg-neutral-50 min-h-screen"
    x-data="{
        activeTab: 'packages',
        selectedItems: {},
        name: '{{ old('name', '') }}',
        phone: '{{ old('phone', '') }}',
        address: '{{ old('address', '') }}',
        schedule_date: '{{ old('schedule_date', date('Y-m-d', strtotime('+1 day'))) }}',
        notes: '{{ old('notes', '') }}',

        init() {
            @if(old('items'))
                const oldItems = @json(old('items'));
                const allServices = @json($packages->concat($units));
                oldItems.forEach(item => {
                    const s = allServices.find(srv => srv.id == item.service_id);
                    if (s) {
                        this.selectedItems[s.id] = {
                            id: s.id,
                            name: s.name,
                            price: s.price,
                            qty: parseInt(item.qty) || 1
                        };
                    }
                });
            @endif
        },

        addItem(service) {
            if (!this.selectedItems[service.id]) {
                this.selectedItems[service.id] = {
                    id: service.id,
                    name: service.name,
                    price: Number(service.price),
                    qty: 1
                };
            } else {
                this.changeQty(service.id, 1);
            }
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
                   this.schedule_date.trim().length > 0;
        }
    }"
>
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <!-- Page Title & Progress Info -->
        <div class="mb-8">
            <span class="text-primary-600 font-semibold text-xs uppercase tracking-wider">Formulir Pemesanan</span>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 tracking-tight mt-1">Pilih Layanan & Jadwal Kunjungan</h1>
            <p class="text-sm text-neutral-600 mt-1">Pilih paket atau layanan satuan di bawah ini, lalu lengkapi alamat dan jadwal yang Anda inginkan.</p>
        </div>

        <form method="POST" action="{{ route('order.store') }}" id="order-form">
            @csrf

            <!-- Hidden input array for server calculation -->
            <template x-for="(item, index) in selectedList" :key="item.id">
                <div>
                    <input type="hidden" :name="'items[' + index + '][service_id]'" :value="item.id">
                    <input type="hidden" :name="'items[' + index + '][qty]'" :value="item.qty">
                </div>
            </template>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Column: Service Selector & Customer Details -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Step 1: Select Services -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-neutral-200 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-primary-600 text-white font-bold text-sm flex items-center justify-center">1</span>
                                <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Pilih Layanan AC</h2>
                            </div>
                            <span class="text-xs text-neutral-500 font-medium" x-text="selectedList.length + ' jenis dipilih'"></span>
                        </div>

                        <!-- Tab Filter: Paket vs Satuan -->
                        <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-neutral-100 border border-neutral-200/80">
                            <button
                                type="button"
                                @click="activeTab = 'packages'"
                                :class="activeTab === 'packages' ? 'bg-white text-primary-600 shadow-xs font-bold' : 'text-neutral-600 font-medium hover:text-neutral-900'"
                                class="flex-1 py-2.5 px-4 rounded-xl text-sm transition duration-150 cursor-pointer text-center"
                            >
                                Paket Hemat
                            </button>
                            <button
                                type="button"
                                @click="activeTab = 'units'"
                                :class="activeTab === 'units' ? 'bg-white text-primary-600 shadow-xs font-bold' : 'text-neutral-600 font-medium hover:text-neutral-900'"
                                class="flex-1 py-2.5 px-4 rounded-xl text-sm transition duration-150 cursor-pointer text-center"
                            >
                                Layanan Satuan
                            </button>
                        </div>

                        <!-- Error Message if items empty -->
                        @error('items')
                            <p class="text-sm text-danger-600 font-medium flex items-center gap-1.5 p-3 rounded-xl bg-rose-50 border border-rose-200">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror

                        <!-- Packages Grid -->
                        <div x-show="activeTab === 'packages'" class="space-y-4">
                            @foreach ($packages as $pkg)
                                <div
                                    class="p-5 rounded-2xl border transition duration-150"
                                    :class="selectedItems[{{ $pkg->id }}] ? 'border-primary-600 bg-primary-50 shadow-xs' : 'border-neutral-200 bg-white hover:border-neutral-300'"
                                >
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="space-y-1.5 flex-1">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-base font-bold text-neutral-900">{{ $pkg->name }}</h3>
                                                @if ($loop->first)
                                                    <span class="px-2 py-0.5 rounded-full bg-accent-500 text-white text-[10px] font-bold">Paling Laris</span>
                                                @endif
                                            </div>
                                            <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed">{{ $pkg->description }}</p>
                                            <div class="text-sm font-bold text-primary-900 pt-1">
                                                {{ format_rupiah($pkg->price) }}
                                            </div>
                                        </div>

                                        <!-- Qty Selector / Add Button -->
                                        <div class="shrink-0 flex items-center justify-end">
                                            <template x-if="!selectedItems[{{ $pkg->id }}]">
                                                <button
                                                    type="button"
                                                    @click="addItem({{ json_encode($pkg) }})"
                                                    class="px-4 py-2 rounded-xl text-sm font-semibold border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white transition cursor-pointer min-h-[44px]"
                                                >
                                                    + Pilih Paket
                                                </button>
                                            </template>

                                            <template x-if="selectedItems[{{ $pkg->id }}]">
                                                <div class="flex items-center gap-3 bg-white p-1 rounded-xl border border-primary-300 shadow-xs">
                                                    <button
                                                        type="button"
                                                        @click="changeQty({{ $pkg->id }}, -1)"
                                                        class="w-9 h-9 rounded-lg bg-neutral-100 hover:bg-neutral-200 text-neutral-800 font-bold flex items-center justify-center transition cursor-pointer text-base"
                                                        aria-label="Kurangi jumlah"
                                                    >
                                                        −
                                                    </button>
                                                    <span class="w-6 text-center text-sm font-bold text-neutral-900" x-text="selectedItems[{{ $pkg->id }}].qty"></span>
                                                    <button
                                                        type="button"
                                                        @click="changeQty({{ $pkg->id }}, 1)"
                                                        class="w-9 h-9 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-bold flex items-center justify-center transition cursor-pointer text-base"
                                                        aria-label="Tambah jumlah"
                                                    >
                                                        +
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Units Grid -->
                        <div x-show="activeTab === 'units'" class="space-y-4" x-cloak>
                            @foreach ($units as $unit)
                                <div
                                    class="p-5 rounded-2xl border transition duration-150"
                                    :class="selectedItems[{{ $unit->id }}] ? 'border-primary-600 bg-primary-50 shadow-xs' : 'border-neutral-200 bg-white hover:border-neutral-300'"
                                >
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="space-y-1.5 flex-1">
                                            <h3 class="text-base font-bold text-neutral-900">{{ $unit->name }}</h3>
                                            <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed">{{ $unit->description }}</p>
                                            <div class="text-sm font-bold text-primary-900 pt-1">
                                                {{ format_rupiah($unit->price) }} / unit
                                            </div>
                                        </div>

                                        <!-- Qty Selector / Add Button -->
                                        <div class="shrink-0 flex items-center justify-end">
                                            <template x-if="!selectedItems[{{ $unit->id }}]">
                                                <button
                                                    type="button"
                                                    @click="addItem({{ json_encode($unit) }})"
                                                    class="px-4 py-2 rounded-xl text-sm font-semibold border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white transition cursor-pointer min-h-[44px]"
                                                >
                                                    + Tambah
                                                </button>
                                            </template>

                                            <template x-if="selectedItems[{{ $unit->id }}]">
                                                <div class="flex items-center gap-3 bg-white p-1 rounded-xl border border-primary-300 shadow-xs">
                                                    <button
                                                        type="button"
                                                        @click="changeQty({{ $unit->id }}, -1)"
                                                        class="w-9 h-9 rounded-lg bg-neutral-100 hover:bg-neutral-200 text-neutral-800 font-bold flex items-center justify-center transition cursor-pointer text-base"
                                                        aria-label="Kurangi jumlah"
                                                    >
                                                        −
                                                    </button>
                                                    <span class="w-6 text-center text-sm font-bold text-neutral-900" x-text="selectedItems[{{ $unit->id }}].qty"></span>
                                                    <button
                                                        type="button"
                                                        @click="changeQty({{ $unit->id }}, 1)"
                                                        class="w-9 h-9 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-bold flex items-center justify-center transition cursor-pointer text-base"
                                                        aria-label="Tambah jumlah"
                                                    >
                                                        +
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Step 2: Customer Details -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-neutral-200 shadow-sm space-y-6">
                        <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                            <span class="w-8 h-8 rounded-full bg-primary-600 text-white font-bold text-sm flex items-center justify-center">2</span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Data Pemesan & Jadwal</h2>
                                <p class="text-xs text-neutral-500">Isi data kunjungan agar teknisi kami dapat menjangkau lokasi Anda.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-1">
                                <x-input
                                    label="Nama Lengkap"
                                    name="name"
                                    placeholder="Contoh: Budi Santoso"
                                    required
                                    x-model="name"
                                />
                            </div>

                            <div class="sm:col-span-1">
                                <x-input
                                    label="Nomor Telepon / WhatsApp"
                                    name="phone"
                                    type="tel"
                                    inputmode="numeric"
                                    placeholder="Contoh: 081234567890"
                                    required
                                    hint="Gunakan nomor yang aktif WhatsApp untuk konfirmasi."
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
                                    required
                                    hint="Teknisi beroperasi antara jam 08.00 s/d 20.00 WIB."
                                    x-model="schedule_date"
                                />
                            </div>

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

                <!-- Right Column: Price Summary (Sticky Desktop) -->
                <div class="lg:col-span-4 sticky top-24 space-y-4">
                    <div class="bg-white rounded-3xl p-6 border border-neutral-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                            <h3 class="text-base font-bold text-neutral-900">Ringkasan Pesanan</h3>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-primary-100 text-primary-700 font-semibold" x-text="selectedList.length + ' item'"></span>
                        </div>

                        <!-- Empty state -->
                        <template x-if="selectedList.length === 0">
                            <div class="text-center py-8 text-neutral-400 space-y-2">
                                <svg class="w-10 h-10 mx-auto text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <p class="text-xs text-neutral-500 font-medium">Belum ada layanan yang dipilih.</p>
                                <p class="text-[11px] text-neutral-400">Silakan klik tombol pilih pada layanan di sebelah kiri.</p>
                            </div>
                        </template>

                        <!-- Selected Items List -->
                        <template x-if="selectedList.length > 0">
                            <div class="space-y-3 divide-y divide-neutral-100 max-h-64 overflow-y-auto pr-1">
                                <template x-for="item in selectedList" :key="item.id">
                                    <div class="pt-2 first:pt-0 flex items-start justify-between gap-3 text-xs">
                                        <div class="flex-1">
                                            <div class="font-bold text-neutral-900" x-text="item.name"></div>
                                            <div class="text-neutral-500 text-[11px]">
                                                <span x-text="item.qty + 'x ' + formatRupiah(item.price)"></span>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <div class="font-bold text-neutral-900" x-text="formatRupiah(item.price * item.qty)"></div>
                                            <button
                                                type="button"
                                                @click="removeItem(item.id)"
                                                class="text-danger-600 hover:underline text-[10px] cursor-pointer"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Subtotal & System Notice -->
                        <div class="border-t border-neutral-100 pt-4 space-y-2 text-xs">
                            <div class="flex items-center justify-between text-neutral-600">
                                <span>Subtotal Layanan:</span>
                                <span class="font-bold text-neutral-900" x-text="formatRupiah(subtotal)"></span>
                            </div>

                            <div class="flex items-center justify-between text-neutral-500">
                                <span>Kode Unik Verifikasi:</span>
                                <span class="italic text-[11px]">Dihitung server (1 - 999)</span>
                            </div>

                            <div class="border-t border-dashed border-neutral-200 pt-3 flex items-baseline justify-between">
                                <div>
                                    <span class="text-xs text-neutral-600 font-medium block">Estimasi Total:</span>
                                    <span class="text-[10px] text-neutral-400 block">+ kode unik QRIS</span>
                                </div>
                                <span class="text-2xl font-black text-primary-900 tracking-tight" x-text="formatRupiah(subtotal)"></span>
                            </div>

                            <p class="text-[11px] text-neutral-500 leading-tight pt-1">
                                * Total final dihitung ulang oleh sistem saat pesanan dibuat.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="!canSubmit()"
                                class="w-full py-3.5 px-4 rounded-xl text-base font-bold text-white bg-accent-500 hover:bg-accent-600 active:bg-accent-700 transition duration-150 disabled:opacity-40 disabled:cursor-not-allowed shadow-md flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <span>Lanjut ke Pembayaran</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Mobile Sticky Bottom Summary Bar -->
    <div
        class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-neutral-200 p-4 z-40 shadow-xl"
        x-show="selectedList.length > 0"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
    >
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">
            <div>
                <span class="text-[11px] text-neutral-500 block leading-tight">Total Sementara (<span x-text="selectedList.length"></span> layanan)</span>
                <span class="text-xl font-bold text-primary-900 leading-tight block" x-text="formatRupiah(subtotal)"></span>
            </div>

            <button
                type="submit"
                form="order-form"
                :disabled="!canSubmit()"
                class="py-3 px-5 rounded-xl text-sm font-bold text-white bg-accent-500 hover:bg-accent-600 transition disabled:opacity-40 disabled:cursor-not-allowed shadow-sm flex items-center gap-1.5 shrink-0 cursor-pointer"
            >
                <span>Lanjut Bayar</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>
</div>
@endsection
