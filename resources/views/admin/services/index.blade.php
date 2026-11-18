@extends('layouts.admin')

@section('title', 'Layanan & Harga')

@php
    $inputClass = 'w-full px-3 h-11 text-sm rounded-xl border border-line bg-paper text-ink focus:outline-none focus:border-blue focus:ring-1 focus:ring-blue';
    $checkboxClass = 'w-4 h-4 rounded border-line text-blue focus:ring-blue';
@endphp

@section('content')
<div class="space-y-8">
    <div>
        <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block mb-1">PENGATURAN</span>
        <h1 class="text-2xl sm:text-3xl font-heading font-medium text-ink">Layanan &amp; Harga</h1>
        <p class="text-sm text-ink mt-2 max-w-3xl">
            Jenis pekerjaan dengan harga <strong>Rp 0</strong> dianggap belum diisi: tidak tampil di website dan tidak bisa dipesan.
            Centang <strong>"Mulai dari"</strong> jika tarif bisa berubah sesuai lokasi atau tingkat kerusakan; harga akan tampil sebagai "Mulai dari Rp …".
        </p>
    </div>

    @if ($errors->any())
        <x-alert type="danger">{{ $errors->first() }}</x-alert>
    @endif

    {{-- Semua input harga di bawah terhubung ke form ini lewat atribut form="form-harga". --}}
    <form id="form-harga" method="POST" action="{{ route('admin.services.bulk-update') }}">
        @csrf
        @method('PUT')
    </form>

    @foreach ($kategoriLayanan as $kategori)
        <section class="bg-paper rounded-2xl border border-line overflow-hidden" aria-labelledby="kategori-{{ $kategori->id }}">
            <div class="px-6 py-5 border-b border-line space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="kategori-{{ $kategori->id }}" class="text-lg font-heading font-medium text-ink">{{ $kategori->nama }}</h2>
                    <span class="text-xs text-ink-mute">{{ $kategori->services->count() }} jenis pekerjaan</span>
                </div>

                <form method="POST" action="{{ route('admin.kategori.update', $kategori) }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                    @csrf
                    @method('PUT')
                    <div class="flex-1">
                        <label for="catatan-{{ $kategori->id }}" class="block text-xs uppercase tracking-wider text-ink-mute mb-1">Catatan harga kategori</label>
                        <textarea
                            id="catatan-{{ $kategori->id }}"
                            name="catatan_harga"
                            rows="2"
                            maxlength="1000"
                            placeholder="Kosongkan jika harga pasti per unit."
                            class="w-full px-3 py-2 text-sm rounded-xl border border-line bg-paper text-ink focus:outline-none focus:border-blue focus:ring-1 focus:ring-blue"
                        >{{ $kategori->catatan_harga }}</textarea>
                    </div>
                    <x-button type="submit" variant="secondary" size="sm" :arrow="false">SIMPAN CATATAN</x-button>
                </form>
            </div>

            <div class="hidden lg:grid grid-cols-12 gap-3 px-6 py-3 bg-ice border-b border-line text-xs uppercase tracking-wider font-semibold text-ink">
                <div class="col-span-4">Jenis pekerjaan</div>
                <div class="col-span-2">Harga (Rp)</div>
                <div class="col-span-2">Satuan</div>
                <div class="col-span-1 text-center">Mulai dari</div>
                <div class="col-span-1 text-center">Aktif</div>
                <div class="col-span-2">Urutan</div>
            </div>

            <ul class="divide-y divide-line">
                @foreach ($kategori->services as $service)
                    <li>
                        @php($f = "services[{$service->id}]")
                        @php($o = "services.{$service->id}")
                        <div class="grid grid-cols-2 lg:grid-cols-12 gap-3 px-6 py-4 lg:items-center">
                            <div class="col-span-2 lg:col-span-4">
                                <p class="text-sm font-semibold text-ink">{{ $service->name }}</p>
                                <p class="text-xs text-ink-mute mt-0.5">
                                    @if (! $service->hasPrice())
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-warning/15 text-ink font-semibold">Harga belum diisi</span>
                                    @elseif (! $service->is_active)
                                        nonaktif &bull; tidak tampil
                                    @else
                                        tampil: {{ $service->priceLabel() }}
                                    @endif
                                </p>
                            </div>

                            <div class="col-span-2 lg:col-span-2">
                                <label for="price-{{ $service->id }}" class="lg:sr-only block text-xs uppercase tracking-wider text-ink-mute mb-1">Harga (Rp)</label>
                                <input id="price-{{ $service->id }}" form="form-harga" type="number" name="{{ $f }}[price]" min="0" step="1000" value="{{ old("$o.price", $service->price) }}" required class="{{ $inputClass }}" />
                            </div>

                            <div class="col-span-1 lg:col-span-2">
                                <label for="satuan-{{ $service->id }}" class="lg:sr-only block text-xs uppercase tracking-wider text-ink-mute mb-1">Satuan</label>
                                <select id="satuan-{{ $service->id }}" form="form-harga" name="{{ $f }}[satuan]" class="{{ $inputClass }}">
                                    @foreach (\App\Models\Service::SATUAN as $satuan)
                                        <option value="{{ $satuan }}" @selected(old("$o.satuan", $service->satuan) === $satuan)>{{ ucfirst($satuan) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="col-span-1 lg:col-span-1 flex lg:justify-center items-center gap-2 text-sm text-ink cursor-pointer min-h-[44px]">
                                <input type="hidden" form="form-harga" name="{{ $f }}[harga_mulai]" value="0">
                                <input type="checkbox" form="form-harga" name="{{ $f }}[harga_mulai]" value="1" @checked(old("$o.harga_mulai", $service->harga_mulai)) class="{{ $checkboxClass }}">
                                <span class="lg:sr-only">Mulai dari</span>
                            </label>

                            <label class="col-span-1 lg:col-span-1 flex lg:justify-center items-center gap-2 text-sm text-ink cursor-pointer min-h-[44px]">
                                <input type="hidden" form="form-harga" name="{{ $f }}[is_active]" value="0">
                                <input type="checkbox" form="form-harga" name="{{ $f }}[is_active]" value="1" @checked(old("$o.is_active", $service->is_active)) class="{{ $checkboxClass }}">
                                <span class="lg:sr-only">Aktif</span>
                            </label>

                            <div class="col-span-1 lg:col-span-2">
                                <label for="urutan-{{ $service->id }}" class="lg:sr-only block text-xs uppercase tracking-wider text-ink-mute mb-1">Urutan</label>
                                <input id="urutan-{{ $service->id }}" form="form-harga" type="number" name="{{ $f }}[sort_order]" min="0" max="9999" value="{{ old("$o.sort_order", $service->sort_order) }}" required class="{{ $inputClass }}" />
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <details class="border-t border-line group">
                <summary class="px-6 py-4 text-xs uppercase tracking-wider font-semibold text-ink cursor-pointer hover:bg-ice list-none">
                    + Tambah jenis pekerjaan di {{ $kategori->nama }}
                </summary>
                <form method="POST" action="{{ route('admin.services.store', $kategori) }}" class="grid grid-cols-2 lg:grid-cols-12 gap-3 px-6 pb-5 lg:items-end">
                    @csrf
                    <div class="col-span-2 lg:col-span-4">
                        <label for="baru-nama-{{ $kategori->id }}" class="block text-xs uppercase tracking-wider text-ink-mute mb-1">Nama pekerjaan</label>
                        <input id="baru-nama-{{ $kategori->id }}" type="text" name="name" maxlength="150" required class="{{ $inputClass }}" />
                    </div>
                    <div class="col-span-2 lg:col-span-2">
                        <label for="baru-harga-{{ $kategori->id }}" class="block text-xs uppercase tracking-wider text-ink-mute mb-1">Harga (Rp)</label>
                        <input id="baru-harga-{{ $kategori->id }}" type="number" name="price" min="0" step="1000" value="0" required class="{{ $inputClass }}" />
                    </div>
                    <div class="col-span-1 lg:col-span-2">
                        <label for="baru-satuan-{{ $kategori->id }}" class="block text-xs uppercase tracking-wider text-ink-mute mb-1">Satuan</label>
                        <select id="baru-satuan-{{ $kategori->id }}" name="satuan" class="{{ $inputClass }}">
                            @foreach (\App\Models\Service::SATUAN as $satuan)
                                <option value="{{ $satuan }}">{{ ucfirst($satuan) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="col-span-1 lg:col-span-2 flex items-center gap-2 text-sm text-ink cursor-pointer min-h-[44px]">
                        <input type="checkbox" name="harga_mulai" value="1" class="{{ $checkboxClass }}">
                        <span>Mulai dari</span>
                    </label>
                    <div class="col-span-2 lg:col-span-2 lg:text-right">
                        <x-button type="submit" variant="primary" size="sm" :arrow="false">TAMBAH</x-button>
                    </div>
                </form>
            </details>
        </section>
    @endforeach

    @if ($kategoriLayanan->flatMap->services->isNotEmpty())
        <div class="sticky bottom-4 z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 py-4 bg-paper rounded-2xl border border-line shadow-lg">
            <p class="text-sm text-ink">Isi semua harga dulu, lalu simpan sekaligus.</p>
            <x-button type="submit" form="form-harga" variant="primary" size="sm" :arrow="false">SIMPAN SEMUA PERUBAHAN</x-button>
        </div>
    @endif

    @if ($layananLama->isNotEmpty())
        <section class="bg-paper rounded-2xl border border-line overflow-hidden">
            <div class="px-6 py-5 border-b border-line">
                <h2 class="text-lg font-heading font-medium text-ink">Layanan lama</h2>
                <p class="text-sm text-ink-mute mt-1">Layanan sebelum struktur kategori. Disimpan agar pesanan lama tetap terbaca, tidak bisa dipesan lagi.</p>
            </div>
            <ul class="divide-y divide-line">
                @foreach ($layananLama as $service)
                    <li class="px-6 py-3 flex items-center justify-between gap-4 text-sm">
                        <span class="text-ink">{{ $service->name }}</span>
                        <span class="text-ink-mute whitespace-nowrap">{{ format_rupiah($service->price) }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
