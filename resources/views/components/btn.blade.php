@props([
    'variant' => 'primary',
    'size' => 'md',
])
@php
    $classes = match($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'ghost' => 'btn-ghost',
        'danger' => 'btn-danger',
        default => 'btn-primary',
    };
    $sizeClass = $size === 'sm' ? 'btn-sm' : '';
@endphp
<button {{ $attributes->merge(['class' => $classes . ' ' . $sizeClass]) }}>
    {{ $slot }}
</button>
