<x-layouts.app>
    <x-slot name="header">Créer un rôle</x-slot>

    <div class="bg-white rounded shadow p-6 max-w-2xl mx-auto">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium">Nom du rôle</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="mt-1 w-full border border-gray-300 rounded p-2"
                    required>
                @error('name')
                    <div class="text-red-600 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block font-medium mb-2">Permissions</label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($permissions as $permission)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                   class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2">{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.roles.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Annuler</a>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
