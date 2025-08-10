@extends('layouts.app')

@section('title', 'Gestion des Rôles')

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-7xl mx-auto mt-8">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
            <x-search-form 
            route="admin.roles.index" 
            placeholder="Rechercher un rôle..."
        />        

            <x-button.primary-button href="{{ route('admin.roles.create') }}" icon="fas fa-plus" color="green">
                Nouveau Rôle
            </x-button.primary-button>    
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-4">Nom</th>
                        <th class="px-6 py-4 text-center">Permissions</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($roles as $role)
                        <tr class="border-t hover:bg-indigo-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $role->name }}</td>
                            <td class="px-6 py-4 flex flex-wrap gap-2 justify-center">
                                @foreach ($role->permissions as $perm)
                                    <span class="flex items-center gap-1 text-xs bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full select-none">
                                        <i class="fas fa-lock text-indigo-600"></i> {{ $perm->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" 
                                    class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-700 transition">
                                    <i class="fas fa-pen-to-square"></i> Modifier
                                </a>
                                
                                <x-delete-button :route="route('admin.roles.destroy', $role->id)" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-gray-500 py-8 italic">
                                Aucun rôle trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $roles->withQueryString()->links() }}
        </div>
    </div>
@endsection
