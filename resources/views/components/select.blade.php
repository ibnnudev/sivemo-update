@props(['id' => '', 'name' => '', 'label' => '', 'isFit' => 'fit', 'required' => false])

<div class="form-control w-full {{ $isFit == 'fit' ? 'max-w-xs' : '' }}">
    @if ($label)
        <label class="label pt-0 pl-0">
            <span class="text-xs font-medium">{{ $label }} {!! $required == true ? '<sup class="text-error">*</sup>' : '' !!}</span>
        </label>
    @endif
    <select name="{{ $name }}" id="{{ $id }}" {!! $attributes->merge([
        'class' => 'select select-bordered text-xs',
    ]) !!}>
        {{ $slot }}
    </select>
    @error($name)
        <label class="label text-xs">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </label>
    @enderror
</div>
