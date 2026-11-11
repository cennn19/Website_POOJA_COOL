@props([
    'status', // can be OrderStatus instance or string
    'size' => 'md',
])

@php
    $statusValue = $status instanceof \App\Enums\OrderStatus ? $status->value : (string) $status;

    $configs = [
        'menunggu_pembayaran' => [
            'label' => 'Menunggu Pembayaran',
            'bg' => 'bg-neutral-100 text-neutral-800 border-neutral-300',
            'dot' => 'bg-neutral-500',
            'icon' => 'clock',
        ],
        'menunggu_verifikasi' => [
            'label' => 'Menunggu Verifikasi',
            'bg' => 'bg-amber-50 text-warning-500 border-amber-300',
            'dot' => 'bg-warning-500',
            'icon' => 'hourglass',
        ],
        'disetujui' => [
            'label' => 'Disetujui',
            'bg' => 'bg-emerald-50 text-success-600 border-emerald-300',
            'dot' => 'bg-success-600',
            'icon' => 'check-circle',
        ],
        'ditolak' => [
            'label' => 'Ditolak',
            'bg' => 'bg-rose-50 text-danger-600 border-rose-300',
            'dot' => 'bg-danger-600',
            'icon' => 'x-circle',
        ],
        'selesai' => [
            'label' => 'Selesai',
            'bg' => 'bg-primary-50 text-primary-700 border-primary-300',
            'dot' => 'bg-primary-600',
            'icon' => 'check-badge',
        ],
    ];

    $cfg = $configs[$statusValue] ?? [
        'label' => ucfirst(str_replace('_', ' ', $statusValue)),
        'bg' => 'bg-neutral-100 text-neutral-800 border-neutral-300',
        'dot' => 'bg-neutral-500',
        'icon' => 'info',
    ];

    $sizeClasses = match ($size) {
        'sm' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3.5 py-1.5 text-sm',
        default => 'px-3 py-1 text-xs',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-medium rounded-full border {$cfg['bg']} {$sizeClasses}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }} shrink-0"></span>
    <span>{{ $cfg['label'] }}</span>
</span>
