@props([
    'padding' => 'p-6',
])

<div
    {{ $attributes->merge([
        'class' => "rounded-2xl border dark:border-neutral-900 border-neutral-100 dark:bg-neutral-950/60 bg-neutral-50/60 shadow-[0_18px_60px_rgba(0,0,0,0.65)] backdrop-blur-sm {$padding}
        "
    ]) }}
>
    {{ $slot }}
</div>
