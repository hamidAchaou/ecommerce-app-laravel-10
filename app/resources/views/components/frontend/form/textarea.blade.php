@props([
    'name',
    'label' => null,
    'rows' => 4,
    'value' => old($name),
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge([
            'class' => 'w-full border-gray-300 rounded-md shadow-sm focus:ring-morocco-blue focus:border-morocco-blue sm:text-sm'
        ]) }}
    >{{ $value }}</textarea>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
