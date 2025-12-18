@props(['striped' => false])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200']) }}>
        @isset($head)
            <thead class="bg-gray-50">
                {{ $head }}
            </thead>
        @endisset
        
        <tbody class="bg-white divide-y divide-gray-200 {{ $striped ? '[&>tr:nth-child(odd)]:bg-gray-50' : '' }}">
            {{ $slot }}
        </tbody>

        @isset($foot)
            <tfoot class="bg-gray-50">
                {{ $foot }}
            </tfoot>
        @endisset
    </table>
</div>
