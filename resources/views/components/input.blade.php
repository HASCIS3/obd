@props(['type' => 'text', 'name', 'value' => null, 'placeholder' => null])

@php
$hasError = $errors->has($name);
$classes = 'block w-full rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
           ($hasError ? 'border-danger-300 text-danger-900 placeholder-danger-300' : 'border-gray-300');
@endphp

<input 
    type="{{ $type }}" 
    name="{{ $name }}" 
    id="{{ $name }}"
    value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => $classes]) }}
>
