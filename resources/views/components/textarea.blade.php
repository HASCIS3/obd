@props(['name', 'value' => null, 'rows' => 3, 'placeholder' => null])

@php
$hasError = $errors->has($name);
$classes = 'block w-full rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
           ($hasError ? 'border-danger-300' : 'border-gray-300');
@endphp

<textarea 
    name="{{ $name }}" 
    id="{{ $name }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => $classes]) }}
>{{ old($name, $value) }}</textarea>
