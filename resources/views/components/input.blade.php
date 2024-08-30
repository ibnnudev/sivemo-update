@props([
    'id' => '',
    'label' => '',
    'name' => '',
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'class' => '',
    'step' => '',
])

<div class="mb-4">
    <label for="{{ $id }}" class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
        {{ $label }} {!! $required == true ? '<span class="text-red-500">*</span>' : '' !!}
    </label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" {!! $attributes->merge([
        'class' =>
            'bg-none border border-gray-300 text-gray-900 text-xs rounded-md focus:ring-purple-600 focus:border-purple-600 block w-full px-2.5 py-2.5' .
            ($class ? ' ' . $class : ''),
    ]) !!}
        placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} autocomplete="off" value="{{ $value }}"
        @if (isset($step)) step="{{ $step }}" @endif>

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>
