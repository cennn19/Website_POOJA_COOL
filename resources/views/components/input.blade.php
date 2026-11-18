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
        <label for="{{ $id }}" class="block text-xs uppercase tracking-[0.12em] text-ink-mute font-medium mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-danger font-semibold">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $type !== 'password' && is_scalar($oldValue = old($name, $value)) ? $oldValue : '' }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->merge([
                'class' => 'w-full px-4 h-12 min-h-[48px] text-base rounded-xl border bg-paper text-ink placeholder:text-ink-mute transition duration-150 focus:outline-none focus:ring-1 ' . 
                    ($hasError 
                        ? 'border-danger ring-1 ring-danger focus:ring-danger' 
                        : 'border-line focus:border-blue focus:ring-blue')
            ]) }}
        />
    </div>

    @if ($hint && ! $hasError)
        <p id="{{ $hintId }}" class="mt-1.5 text-xs text-ink-mute">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $errorId }}" class="mt-1.5 text-[13px] text-danger flex items-center gap-1 font-medium">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
