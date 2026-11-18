@extends('layouts.admin')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs uppercase tracking-[0.12em] text-ink-mute font-medium block">ADMINISTRASI</span>
            <h1 class="text-2xl sm:text-3xl font-heading font-medium text-ink tracking-tight mt-1">Daftar Pesanan Layanan</h1>
            <p class="text-xs text-ink-mute mt-1">Kelola pesanan pelanggan, verifikasi mutasi QRIS, dan pantau penyelesaian teknisi.</p>
        </div>
    </div>

    <!-- Status Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
        <a
            href="{{ route('admin.orders.index') }}"
            class="p-4 rounded-xl border transition {{ !request('status') ? 'bg-paper border-ink ring-1 ring-ink' : 'bg-paper border-line hover:border-ink/50' }}"
        >
            <span class="text-[11px] uppercase tracking-wider text-ink-mute font-medium block">Semua</span>
            <span class="text-2xl font-heading font-medium text-ink mt-0.5 block">{{ $counts['total'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'menunggu_verifikasi']) }}"
            class="p-4 rounded-xl border transition {{ request('status') === 'menunggu_verifikasi' ? 'bg-paper border-warning ring-1 ring-warning' : 'bg-paper border-line hover:border-warning/50' }}"
        >
            <span class="text-[11px] uppercase tracking-wider text-warning font-medium block">Perlu Verifikasi</span>
            <span class="text-2xl font-heading font-medium text-ink mt-0.5 block">{{ $counts['menunggu_verifikasi'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'menunggu_pembayaran']) }}"
            class="p-4 rounded-xl border transition {{ request('status') === 'menunggu_pembayaran' ? 'bg-paper border-ink-mute ring-1 ring-ink-mute' : 'bg-paper border-line hover:border-ink/40' }}"
        >
            <span class="text-[11px] uppercase tracking-wider text-ink-mute font-medium block">Belum Bayar</span>
            <span class="text-2xl font-heading font-medium text-ink mt-0.5 block">{{ $counts['menunggu_pembayaran'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'disetujui']) }}"
            class="p-4 rounded-xl border transition {{ request('status') === 'disetujui' ? 'bg-paper border-success ring-1 ring-success' : 'bg-paper border-line hover:border-success/50' }}"
        >
            <span class="text-[11px] uppercase tracking-wider text-success font-medium block">Disetujui</span>
            <span class="text-2xl font-heading font-medium text-ink mt-0.5 block">{{ $counts['disetujui'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'ditolak']) }}"
            class="p-4 rounded-xl border transition {{ request('status') === 'ditolak' ? 'bg-paper border-danger ring-1 ring-danger' : 'bg-paper border-line hover:border-danger/50' }}"
        >
            <span class="text-[11px] uppercase tracking-wider text-danger font-medium block">Ditolak</span>
            <span class="text-2xl font-heading font-medium text-ink mt-0.5 block">{{ $counts['ditolak'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'selesai']) }}"
            class="p-4 rounded-xl border transition {{ request('status') === 'selesai' ? 'bg-paper border-ink ring-1 ring-ink' : 'bg-paper border-line hover:border-ink/50' }}"
        >
            <span class="text-[11px] uppercase tracking-wider text-ink font-medium block">Selesai</span>
            <span class="text-2xl font-heading font-medium text-ink mt-0.5 block">{{ $counts['selesai'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'dibatalkan']) }}"
            class="p-4 rounded-xl border transition {{ request('status') === 'dibatalkan' ? 'bg-line border-ink ring-1 ring-ink' : 'bg-paper border-line hover:border-ink/50' }}"
        >
            <span class="text-[11px] uppercase tracking-wider text-ink-mute font-medium block">Dibatalkan</span>
            <span class="text-2xl font-heading font-medium text-ink mt-0.5 block">{{ $counts['dibatalkan'] }}</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-paper rounded-2xl p-4 border border-line">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari kode order (HVC-...), nama pemesan, atau telepon..."
                    class="w-full pl-4 pr-4 py-2.5 text-sm rounded-xl bg-mist border-0 text-ink placeholder:text-ink-mute focus:outline-none focus:ring-2 focus:ring-ink"
                />
            </div>

            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @else
                <label class="flex items-center gap-2 text-sm text-ink whitespace-nowrap cursor-pointer">
                    <input type="checkbox" name="tampilkan_batal" value="1" @checked(request()->boolean('tampilkan_batal')) x-data @change="$el.form.submit()" class="w-4 h-4 rounded border-line text-blue focus:ring-blue">
                    Tampilkan dibatalkan
                </label>
            @endif

            <div class="flex items-center gap-2">
                <x-button type="submit" variant="primary" size="sm">
                    CARI
                </x-button>
                @if(request('q') || request('status') || request('tampilkan_batal'))
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="px-4 py-2 rounded-full text-xs uppercase tracking-wider text-ink-mute hover:text-ink transition"
                    >
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table (Desktop) -->
    <div class="hidden md:block bg-paper rounded-2xl border border-line overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-mist border-b border-line text-xs uppercase tracking-[0.12em] text-ink-mute font-medium">
                <tr>
                    <th class="px-6 py-3.5">Kode Order</th>
                    <th class="px-6 py-3.5">Pelanggan</th>
                    <th class="px-6 py-3.5">Jadwal Kunjungan</th>
                    <th class="px-6 py-3.5">Total Bayar</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line/70">
                @forelse ($orders as $order)
                    <tr class="hover:bg-mist/50 transition">
                        <td class="px-6 py-4 font-mono font-medium text-ink">
                            {{ $order->code }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-ink">{{ $order->name }}</div>
                            <div class="text-xs text-ink-mute">{{ $order->phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-ink">{{ $order->schedule_date->translatedFormat('d M Y') }}</div>
                            @if ($order->sesi)
                                <div class="text-xs text-ink-mute">Sesi {{ ucfirst($order->sesi) }}</div>
                            @endif
                            <div class="text-[11px] text-ink-mute">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium text-ink">
                            {{ format_rupiah($order->total) }}
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$order->status" />
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a
                                href="{{ route('admin.orders.show', $order) }}"
                                class="inline-flex items-center gap-1 text-xs uppercase tracking-wider font-semibold text-ink hover:underline underline-offset-4"
                            >
                                <span>Detail</span>
                                <span>↗</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-ink-mute">
                            Tidak ada pesanan yang sesuai kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Orders Card List -->
    <div class="md:hidden space-y-3">
        @forelse ($orders as $order)
            <div class="bg-paper rounded-2xl p-4 border border-line space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-medium text-sm text-ink">{{ $order->code }}</span>
                    <x-status-badge :status="$order->status" size="sm" />
                </div>

                <div class="space-y-1 text-xs">
                    <div class="text-sm font-medium text-ink">{{ $order->name }}</div>
                    <div class="text-ink-mute">{{ $order->phone }} &bull; Jadwal: {{ $order->schedule_date->translatedFormat('d M Y') }}{{ $order->sesi ? ' (' . ucfirst($order->sesi) . ')' : '' }}</div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-line/60">
                    <span class="font-medium text-sm text-ink">{{ format_rupiah($order->total) }}</span>
                    <a
                        href="{{ route('admin.orders.show', $order) }}"
                        class="px-3 py-1.5 rounded-full bg-ink text-paper text-xs uppercase tracking-wider font-semibold hover:bg-blue transition"
                    >
                        Lihat & Verifikasi
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center bg-paper rounded-2xl border border-line text-ink-mute text-sm">
                Tidak ada pesanan.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>
@endsection
