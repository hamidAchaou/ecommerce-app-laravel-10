{{-- resources/views/components/frontend/form/select.blade.php --}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => old($name),
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'w-full border-gray-300 rounded-md shadow-sm focus:ring-morocco-blue focus:border-morocco-blue sm:text-sm'
        ]) }}
    >
        <option value="">-- Select --</option>
        @foreach($options as $key => $value)
            <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
