@props(['role' => null, 'permissions', 'action', 'method'])

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-6">
        <label for="name" class="block text-gray-700 font-medium mb-2">Nom du rôle</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $role->name ?? '') }}"
            required
            placeholder="Entrez le nom du rôle"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
        />
        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-8">
        <span class="block text-gray-700 font-medium mb-3">Permissions</span>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-4 bg-gray-50">
            @foreach ($permissions as $permission)
                <label class="flex items-center space-x-3 cursor-pointer hover:bg-indigo-100 rounded-md px-3 py-2 transition">
                    <input
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission->id }}"
                        class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        {{ (in_array($permission->id, old('permissions', $role ? $role->permissions->pluck('id')->toArray() : []))) ? 'checked' : '' }}
                    />
                    <span class="text-gray-800 font-medium select-none">{{ $permission->name }}</span>
                </label>
            @endforeach
        </div>
        @error('permissions')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('admin.roles.index') }}"
            class="inline-block px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
            class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            Enregistrer
        </button>
    </div>
</form>
