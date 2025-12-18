@props(['color' => 'gray', 'size' => 'md'])

@php
$colorClasses = match($color) {
    'primary' => 'bg-primary-100 text-primary-800',
    'secondary' => 'bg-secondary-100 text-secondary-800',
    'danger' => 'bg-danger-100 text-danger-800',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'info' => 'bg-blue-100 text-blue-800',
    'gray' => 'bg-gray-100 text-gray-800',
    default => 'bg-gray-100 text-gray-800',
};

$sizeClasses = match($size) {
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-sm',
    'lg' => 'px-3 py-1 text-sm',
    default => 'px-2.5 py-0.5 text-sm',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full $colorClasses $sizeClasses"]) }}>
    {{ $slot }}
</span>
