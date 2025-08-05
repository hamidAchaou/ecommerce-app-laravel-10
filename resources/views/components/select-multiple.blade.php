@props(['id', 'label', 'name', 'options', 'old' => []])

<div>
    <label for="{{ $id }}" class="block font-medium mb-2">{{ $label }}</label>

    <div class="flex justify-end mb-2 gap-2">
        <button type="button" onclick="selectAll('{{ $id }}')"
            class="text-sm px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            Tout sélectionner
        </button>
        <button type="button" onclick="deselectAll('{{ $id }}')"
            class="text-sm px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
            Tout désélectionner
        </button>
    </div>

    <select name="{{ $name }}" id="{{ $id }}" multiple
        placeholder="Sélectionnez {{ strtolower($label) }}..."
        class="choices w-full border border-gray-300 rounded-md p-2">
        @foreach ($options as $option)
            <option value="{{ $option->name }}" {{ is_array($old) && in_array($option->name, $old) ? 'selected' : '' }}>
                {{ $option->name }}
            </option>
        @endforeach
    </select>
</div>
