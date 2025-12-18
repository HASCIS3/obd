@props(['name', 'options' => [], 'selected' => null, 'placeholder' => 'Selectionner...', 'valueKey' => 'id', 'labelKey' => 'name'])

@php
$hasError = $errors->has($name);
$classes = 'block w-full rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
           ($hasError ? 'border-danger-300' : 'border-gray-300');
$selectedValue = old($name, $selected);
@endphp

<select name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    
    @foreach($options as $option)
        @php
            $optionValue = is_array($option) || is_object($option) ? data_get($option, $valueKey) : $option;
            $optionLabel = is_array($option) || is_object($option) ? data_get($option, $labelKey) : $option;
        @endphp
        <option value="{{ $optionValue }}" {{ $selectedValue == $optionValue ? 'selected' : '' }}>
            {{ $optionLabel }}
        </option>
    @endforeach
</select>
