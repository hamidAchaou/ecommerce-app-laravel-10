@props(['permission' => null, 'action', 'method'])

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-6">
        <label for="name" class="block text-gray-700 font-medium mb-2">Nom de la permission</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $permission->name ?? '') }}"
            required
            placeholder="Entrez le nom de la permission"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
        />
        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('admin.permissions.index') }}"
            class="inline-block px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
            class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            Enregistrer
        </button>
    </div>
</form>
