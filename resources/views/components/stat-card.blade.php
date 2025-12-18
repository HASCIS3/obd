@props(['title', 'value', 'icon' => null, 'color' => 'primary', 'trend' => null, 'trendValue' => null])

@php
$colorClasses = match($color) {
    'primary' => 'bg-primary-500 text-white',
    'secondary' => 'bg-secondary-500 text-primary-900',
    'danger' => 'bg-danger-500 text-white',
    'success' => 'bg-green-500 text-white',
    'info' => 'bg-blue-500 text-white',
    default => 'bg-gray-500 text-white',
};

$iconBg = match($color) {
    'primary' => 'bg-primary-600',
    'secondary' => 'bg-secondary-600',
    'danger' => 'bg-danger-600',
    'success' => 'bg-green-600',
    'info' => 'bg-blue-600',
    default => 'bg-gray-600',
};
@endphp

<div class="relative overflow-hidden rounded-lg {{ $colorClasses }} p-5 shadow">
    <div class="flex items-center">
        @if($icon)
            <div class="flex-shrink-0 rounded-md {{ $iconBg }} p-3">
                {!! $icon !!}
            </div>
        @endif
        <div class="{{ $icon ? 'ml-5' : '' }} w-0 flex-1">
            <dl>
                <dt class="truncate text-sm font-medium opacity-90">{{ $title }}</dt>
                <dd class="mt-1 text-3xl font-bold">{{ $value }}</dd>
            </dl>
        </div>
    </div>
    @if($trend)
        <div class="mt-3 flex items-center text-sm">
            @if($trend === 'up')
                <svg class="h-4 w-4 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            @else
                <svg class="h-4 w-4 text-red-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            @endif
            <span class="ml-1 opacity-90">{{ $trendValue }}</span>
        </div>
    @endif
</div>
