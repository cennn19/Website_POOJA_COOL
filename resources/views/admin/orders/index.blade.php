@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Daftar Pesanan Layanan AC</h1>
            <p class="text-xs text-neutral-500 mt-1">Kelola pesanan pelanggan, verifikasi bukti bayar QRIS, dan pantau status teknisi.</p>
        </div>
    </div>

    <!-- Status Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a
            href="{{ route('admin.orders.index') }}"
            class="p-4 rounded-2xl border transition {{ !request('status') ? 'bg-primary-50 border-primary-300 ring-2 ring-primary-500/20' : 'bg-white border-neutral-200 hover:border-neutral-300' }}"
        >
            <span class="text-xs font-semibold text-neutral-500 block">Semua</span>
            <span class="text-2xl font-bold text-neutral-900">{{ $counts['total'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'menunggu_verifikasi']) }}"
            class="p-4 rounded-2xl border transition {{ request('status') === 'menunggu_verifikasi' ? 'bg-amber-50 border-amber-400 ring-2 ring-amber-400/20' : 'bg-white border-neutral-200 hover:border-neutral-300' }}"
        >
            <span class="text-xs font-semibold text-warning-500 block">Menunggu Verifikasi</span>
            <span class="text-2xl font-bold text-neutral-900">{{ $counts['menunggu_verifikasi'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'menunggu_pembayaran']) }}"
            class="p-4 rounded-2xl border transition {{ request('status') === 'menunggu_pembayaran' ? 'bg-neutral-100 border-neutral-400 ring-2 ring-neutral-400/20' : 'bg-white border-neutral-200 hover:border-neutral-300' }}"
        >
            <span class="text-xs font-semibold text-neutral-600 block">Belum Bayar</span>
            <span class="text-2xl font-bold text-neutral-900">{{ $counts['menunggu_pembayaran'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'disetujui']) }}"
            class="p-4 rounded-2xl border transition {{ request('status') === 'disetujui' ? 'bg-emerald-50 border-emerald-400 ring-2 ring-emerald-400/20' : 'bg-white border-neutral-200 hover:border-neutral-300' }}"
        >
            <span class="text-xs font-semibold text-success-600 block">Disetujui</span>
            <span class="text-2xl font-bold text-neutral-900">{{ $counts['disetujui'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'ditolak']) }}"
            class="p-4 rounded-2xl border transition {{ request('status') === 'ditolak' ? 'bg-rose-50 border-rose-400 ring-2 ring-rose-400/20' : 'bg-white border-neutral-200 hover:border-neutral-300' }}"
        >
            <span class="text-xs font-semibold text-danger-600 block">Ditolak</span>
            <span class="text-2xl font-bold text-neutral-900">{{ $counts['ditolak'] }}</span>
        </a>

        <a
            href="{{ route('admin.orders.index', ['status' => 'selesai']) }}"
            class="p-4 rounded-2xl border transition {{ request('status') === 'selesai' ? 'bg-primary-50 border-primary-400 ring-2 ring-primary-400/20' : 'bg-white border-neutral-200 hover:border-neutral-300' }}"
        >
            <span class="text-xs font-semibold text-primary-600 block">Selesai</span>
            <span class="text-2xl font-bold text-neutral-900">{{ $counts['selesai'] }}</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-neutral-200 shadow-xs">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari kode order (HVC-...), nama pelanggan, atau nomor telepon..."
                    class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-neutral-200 focus:outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-500"
                />
                <svg class="w-4 h-4 text-neutral-400 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 transition cursor-pointer min-h-[38px]"
                >
                    Cari
                </button>
                @if(request('q') || request('status'))
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="px-3 py-2 rounded-xl text-sm font-medium text-neutral-600 hover:bg-neutral-100 transition"
                    >
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table (Desktop) -->
    <div class="hidden md:block bg-white rounded-3xl border border-neutral-200 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-600 text-xs uppercase tracking-wider font-semibold">
                <tr>
                    <th class="px-6 py-4">Kode Order</th>
                    <th class="px-6 py-4">Pelanggan</th>
                    <th class="px-6 py-4">Jadwal Kunjungan</th>
                    <th class="px-6 py-4">Total Bayar</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($orders as $order)
                    <tr class="hover:bg-neutral-50/60 transition">
                        <td class="px-6 py-4 font-mono font-bold text-primary-900">
                            {{ $order->code }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-neutral-900">{{ $order->name }}</div>
                            <div class="text-xs text-neutral-500">{{ $order->phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-neutral-800">{{ $order->schedule_date->translatedFormat('d M Y') }}</div>
                            <div class="text-[11px] text-neutral-500">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-neutral-900">
                            {{ format_rupiah($order->total) }}
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$order->status" />
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a
                                href="{{ route('admin.orders.show', $order) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-neutral-200 text-xs font-semibold text-primary-600 hover:bg-primary-50 hover:border-primary-300 transition"
                            >
                                <span>Detail</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-neutral-400">
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
            <div class="bg-white rounded-2xl p-4 border border-neutral-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-sm text-primary-900">{{ $order->code }}</span>
                    <x-status-badge :status="$order->status" size="sm" />
                </div>

                <div class="space-y-1 text-xs">
                    <div class="text-sm font-bold text-neutral-900">{{ $order->name }}</div>
                    <div class="text-neutral-500">{{ $order->phone }} &bull; Jadwal: {{ $order->schedule_date->translatedFormat('d M Y') }}</div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-neutral-100">
                    <span class="font-extrabold text-sm text-neutral-900">{{ format_rupiah($order->total) }}</span>
                    <a
                        href="{{ route('admin.orders.show', $order) }}"
                        class="px-3 py-1.5 rounded-lg bg-primary-600 text-white text-xs font-bold hover:bg-primary-700 transition"
                    >
                        Lihat & Verifikasi
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center bg-white rounded-2xl border border-neutral-200 text-neutral-400 text-sm">
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
