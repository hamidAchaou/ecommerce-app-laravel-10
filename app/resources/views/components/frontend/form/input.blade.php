@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false])

<div>
    <label for="{{ $name }}" class="block font-medium mb-2">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        class="w-full border border-gray-300 rounded-md p-2"
        {{ $required ? 'required' : '' }}
    >
</div>
