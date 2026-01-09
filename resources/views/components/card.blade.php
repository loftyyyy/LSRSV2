@props([
    'padding' => 'p-6',
])

<div
    {{ $attributes->merge([
        'class' => "rounded-2xl border border-neutral-900
            bg-neutral-950/60
            shadow-[0_18px_60px_rgba(0,0,0,0.65)]
            backdrop-blur-sm
            {$padding}
        "
    ]) }}
>
    {{ $slot }}
</div>
