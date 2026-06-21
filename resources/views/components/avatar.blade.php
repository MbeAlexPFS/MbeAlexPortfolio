@props(['user', 'size' => 'md', 'class' => ''])

@php
    $sizes = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-14 h-14 text-lg',
        'xl' => 'w-20 h-20 text-2xl',
        '2xl' => 'w-32 h-32 text-4xl',
    ];

    $containerClass = $sizes[$size] ?? $sizes['md'];

    $initials = strtoupper(
        collect(explode(' ', $user->pseudo))
            ->map(fn($part) => substr($part, 0, 1))
            ->take(2)
            ->implode('')
    );
@endphp

@if($user->avatar_url)
    <img src="{{ $user->avatar_url }}"
         alt="{{ $user->pseudo }}"
         class="{{ $containerClass }} rounded-full object-cover flex-shrink-0 {{ $class }}">
@else
    <div class="{{ $containerClass }} rounded-full bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-300 font-semibold flex items-center justify-center flex-shrink-0 {{ $class }}">
        {{ $initials }}
    </div>
@endif
