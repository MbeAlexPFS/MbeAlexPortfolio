@props([
    'type' => 'submit',
    'variant' => 'primary',
    'size' => 'md',
    'loadingText' => 'Chargement...',
    'loading' => false,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition rounded-lg';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-2.5 text-sm',
    ];

    $variants = [
        'primary' => 'bg-indigo-600 dark:bg-indigo-500 text-white hover:bg-indigo-700 dark:hover:bg-indigo-600',
        'secondary' => 'border border-gray-300 dark:border-dark-border text-gray-700 dark:text-dark-muted hover:border-gray-400 dark:hover:border-dark-muted',
        'danger' => 'text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white',
        'warning' => 'text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300',
        'ghost' => 'text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button type="{{ $type }}" {{ $attributes->class([$baseClasses, $sizeClass, $variantClass]) }}
        x-data="{ loading: {{ $loading ? 'true' : 'false' }} }"
        x-init="$nextTick(() => {
            $el.closest('form')?.addEventListener('submit', () => { loading = true; });
        })"
        x-bind:disabled="loading || {{ $disabled ? 'true' : 'false' }}"
        x-bind:class="(loading || {{ $disabled ? 'true' : 'false' }}) && 'opacity-50 cursor-not-allowed'">
    <span x-show="!loading">{{ $slot }}</span>
    <span x-show="loading" x-cloak class="inline-flex items-center gap-1.5" role="status">
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        {{ $loadingText }}
    </span>
</button>
