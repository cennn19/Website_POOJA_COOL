@props([
    'variant' => 'primary', // primary, accent, secondary, danger, outline
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition duration-150 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 min-h-[44px] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none';
    
    $variantClasses = match ($variant) {
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 active:bg-primary-800 focus-visible:ring-primary-500 shadow-sm',
        'accent' => 'bg-accent-500 text-white hover:bg-accent-600 active:bg-accent-700 focus-visible:ring-accent-500 shadow-sm',
        'secondary' => 'border-2 border-primary-600 text-primary-600 bg-transparent hover:bg-primary-50 active:bg-primary-100 focus-visible:ring-primary-500',
        'danger' => 'bg-danger-600 text-white hover:bg-red-700 active:bg-red-800 focus-visible:ring-danger-600 shadow-sm',
        'neutral' => 'bg-neutral-200 text-neutral-900 hover:bg-neutral-300 active:bg-neutral-400 focus-visible:ring-neutral-400',
        default => 'bg-primary-600 text-white hover:bg-primary-700 focus-visible:ring-primary-500',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3.5 py-1.5 text-sm gap-1.5',
        'lg' => 'px-6 py-3.5 text-lg gap-2.5',
        default => 'px-5 py-2.5 text-base gap-2',
    };

    $classes = "{$baseClasses} {$variantClasses} {$sizeClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
