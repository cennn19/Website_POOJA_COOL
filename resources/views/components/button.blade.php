@props([
    'variant' => 'primary', // primary, secondary, white, outline-white, danger, text
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'arrow' => true,
])

@php
    $baseClasses = 'inline-flex items-center justify-center uppercase font-semibold tracking-wider rounded-full border transition duration-150 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-blue focus-visible:ring-offset-2 min-h-[44px] cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed select-none group';

    $variantClasses = match ($variant) {
        'primary' => 'bg-ink border-ink text-paper hover:bg-blue hover:border-blue active:bg-blue-hover',
        'secondary' => 'bg-transparent border-ink text-ink hover:bg-ink hover:text-paper',
        'white' => 'bg-paper border-paper text-ink hover:bg-ice hover:border-ice',
        'outline-white' => 'bg-transparent border-paper text-paper hover:bg-paper hover:text-ink',
        'danger' => 'bg-danger border-danger text-paper hover:bg-paper hover:text-danger',
        'text' => 'bg-transparent border-transparent text-blue hover:text-blue-hover p-0 min-h-0 underline-offset-4',
        default => 'bg-ink border-ink text-paper hover:bg-blue hover:border-blue',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-4 py-2 text-[11px] gap-1.5',
        'lg' => 'px-7 py-3.5 text-xs gap-2',
        default => 'px-5 py-3 text-xs gap-1.5',
    };

    if ($variant === 'text') {
        $sizeClasses = 'text-xs gap-1 py-1';
    }

    $classes = "{$baseClasses} {$variantClasses} {$sizeClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        <span>{{ $slot }}</span>
        @if ($arrow)
            <span class="inline-block transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 text-sm leading-none" aria-hidden="true">↗</span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        <span>{{ $slot }}</span>
        @if ($arrow)
            <span class="inline-block transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 text-sm leading-none" aria-hidden="true">↗</span>
        @endif
    </button>
@endif
