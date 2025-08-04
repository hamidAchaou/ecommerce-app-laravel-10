<x-layouts.app>
    <x-slot name="header">Gestion des rôles</x-slot>

    <div class="bg-white rounded shadow p-6">
        <div class="flex justify-between mb-4">
            <form method="GET" class="flex space-x-2">
                <input type="search" name="search" placeholder="Chercher un rôle..."
                       value="{{ request('search') }}"
                       class="border border-gray-300 rounded p-2">
                <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Rechercher
                </button>
            </form>
            <a href="{{ route('admin.roles.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Nouveau Rôle</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Permissions</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $role->name }}</td>
                            <td class="px-4 py-2">
                                @foreach($role->permissions as $perm)
                                    <span class="text-sm bg-indigo-100 text-indigo-800 px-2 py-1 rounded">
                                        {{ $perm->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('admin.roles.edit', $role->id) }}"
                                   class="text-yellow-600 hover:underline">Modifier</a>
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:underline"
                                            onclick="return confirm('Supprimer ce rôle ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $roles->withQueryString()->links() }}
        </div>
    </div>
</x-layouts.app>
