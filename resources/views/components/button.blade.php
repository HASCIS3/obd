@props(['type' => 'button', 'variant' => 'primary', 'size' => 'md', 'href' => null])

@php
$baseClasses = 'inline-flex items-center justify-center font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = match($variant) {
    'primary' => 'bg-primary-500 text-white hover:bg-primary-600 focus:ring-primary-500',
    'secondary' => 'bg-secondary-500 text-primary-900 hover:bg-secondary-400 focus:ring-secondary-500',
    'danger' => 'bg-danger-500 text-white hover:bg-danger-600 focus:ring-danger-500',
    'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    'outline' => 'border-2 border-primary-500 text-primary-500 hover:bg-primary-50 focus:ring-primary-500',
    'outline-danger' => 'border-2 border-danger-500 text-danger-500 hover:bg-danger-50 focus:ring-danger-500',
    'ghost' => 'text-gray-600 hover:bg-gray-100 focus:ring-gray-500',
    default => 'bg-gray-500 text-white hover:bg-gray-600 focus:ring-gray-500',
};

$sizeClasses = match($size) {
    'xs' => 'px-2.5 py-1.5 text-xs',
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-4 py-2 text-base',
    'xl' => 'px-6 py-3 text-base',
    default => 'px-4 py-2 text-sm',
};

$classes = "$baseClasses $variantClasses $sizeClasses";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
