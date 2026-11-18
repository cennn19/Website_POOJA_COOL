@props([
    'status', // can be OrderStatus instance or string
    'size' => 'md',
])

@php
    $statusValue = $status instanceof \App\Enums\OrderStatus ? $status->value : (string) $status;

    $configs = [
        'menunggu_pembayaran' => [
            'label' => 'Menunggu Pembayaran',
            'bg' => 'bg-slate text-paper',
            'dot' => 'bg-paper/70',
        ],
        'menunggu_verifikasi' => [
            'label' => 'Menunggu Verifikasi',
            'bg' => 'bg-st-pending text-paper',
            'dot' => 'bg-paper/70',
        ],
        'disetujui' => [
            'label' => 'Disetujui',
            'bg' => 'bg-st-approved text-paper',
            'dot' => 'bg-paper/70',
        ],
        'ditolak' => [
            'label' => 'Ditolak',
            'bg' => 'bg-st-rejected text-paper',
            'dot' => 'bg-paper/70',
        ],
        'dibatalkan' => [
            'label' => 'Dibatalkan',
            'bg' => 'bg-line text-ink',
            'dot' => 'bg-ink-mute',
        ],
        'selesai' => [
            'label' => 'Selesai',
            'bg' => 'bg-ink text-paper',
            'dot' => 'bg-paper/70',
        ],
    ];

    $cfg = $configs[$statusValue] ?? [
        'label' => ucfirst(str_replace('_', ' ', $statusValue)),
        'bg' => 'bg-slate text-paper',
        'dot' => 'bg-paper/70',
    ];

    $sizeClasses = match ($size) {
        'sm' => 'px-2 py-0.5 text-[10px]',
        'lg' => 'px-3.5 py-1 text-xs',
        default => 'px-2.5 py-1 text-[11px]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-medium uppercase tracking-wider rounded-full {$cfg['bg']} {$sizeClasses} select-none"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }} shrink-0"></span>
    <span>{{ $cfg['label'] }}</span>
</span>
