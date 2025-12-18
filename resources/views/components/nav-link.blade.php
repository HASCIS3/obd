@props(['active', 'href' => '#'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-2 py-1.5 rounded-md text-xs font-medium bg-secondary-500 text-primary-900 focus:outline-none transition duration-150 ease-in-out whitespace-nowrap'
            : 'inline-flex items-center px-2 py-1.5 rounded-md text-xs font-medium text-white hover:bg-primary-600 hover:text-secondary-300 focus:outline-none focus:bg-primary-600 transition duration-150 ease-in-out whitespace-nowrap';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
