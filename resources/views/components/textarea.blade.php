@props(['id' => '', 'name' => '', 'label' => '', 'required' => false, 'value' => '', 'disabled' => false])

<div class="mb-4">
    <label for="{{ $id }}" class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
        {{ $label }} {!! $required ? '<span class="text-red-500">*</span>' : '' !!}
    </label>
    <textarea id="{{ $id }}" rows="4" name="{{ $name }}" {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="mb-3 p-2.5 bg-none border border-gray-300 text-xs text-gray-900 rounded-md focus:ring-primary-600 focus:border-primary-600 block w-full">{{ $value }}</textarea>

    @error($name)
        <div class="text-red-500 text-xs">
            {{ $message }}
        </div>
    @enderror
</div>
