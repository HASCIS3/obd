@props(['title' => null, 'subtitle' => null, 'footer' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200']) }}>
    @if($title || isset($header))
        <div class="px-4 py-4 sm:px-6 border-b border-gray-200 bg-gray-50">
            @isset($header)
                {{ $header }}
            @else
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            @endisset
        </div>
    @endif

    <div class="{{ $padding ? 'p-4 sm:p-6' : '' }}">
        {{ $slot }}
    </div>

    @if($footer || isset($footerSlot))
        <div class="px-4 py-3 sm:px-6 border-t border-gray-200 bg-gray-50">
            @isset($footerSlot)
                {{ $footerSlot }}
            @else
                {{ $footer }}
            @endisset
        </div>
    @endif
</div>
