@props([
    'type' => 'info', // info, success, warning, danger
    'dismissible' => true,
])

@php
    $configs = [
        'info' => [
            'wrap' => 'bg-primary-50 border-primary-200 text-primary-900',
            'iconColor' => 'text-primary-600',
            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'success' => [
            'wrap' => 'bg-emerald-50 border-emerald-200 text-emerald-950',
            'iconColor' => 'text-success-600',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'warning' => [
            'wrap' => 'bg-amber-50 border-amber-200 text-amber-950',
            'iconColor' => 'text-warning-500',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
        'danger' => [
            'wrap' => 'bg-rose-50 border-rose-200 text-rose-950',
            'iconColor' => 'text-danger-600',
            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    $cfg = $configs[$type] ?? $configs['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-2xl border {$cfg['wrap']} shadow-sm"]) }}
    role="alert"
>
    <svg class="w-5 h-5 shrink-0 mt-0.5 {{ $cfg['iconColor'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}" />
    </svg>

    <div class="flex-1 text-sm leading-relaxed">
        {{ $slot }}
    </div>

    @if ($dismissible)
        <button
            type="button"
            @click="show = false"
            class="shrink-0 p-1 rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-black/5 transition"
            aria-label="Tutup notifikasi"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
