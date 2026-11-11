@props([
    'label' => null,
    'id' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'hint' => null,
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
    $errorId = "{$id}-error";
    $hintId = "{$id}-hint";
    $describedBy = $hasError ? $errorId : ($hint ? $hintId : null);
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-neutral-900 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-danger-600 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-2.5 min-h-[44px] text-base rounded-xl border transition duration-150 ease-in-out text-neutral-900 placeholder:text-neutral-600 bg-white focus:outline-none focus:ring-2 ' . 
                    ($hasError 
                        ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600/30' 
                        : 'border-neutral-200 focus:border-primary-600 focus:ring-primary-500/30 hover:border-neutral-300')
            ]) }}
        />
    </div>

    @if ($hint && ! $hasError)
        <p id="{{ $hintId }}" class="mt-1 text-xs text-neutral-600">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $errorId }}" class="mt-1.5 text-sm text-danger-600 flex items-center gap-1 font-medium">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
