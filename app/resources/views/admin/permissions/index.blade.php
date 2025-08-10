@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-7xl mx-auto mt-8">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
            <x-search-form 
                route="admin.permissions.index" 
                placeholder="Rechercher une permission..." 
                class="flex w-full md:w-auto"
            />

            <x-button.primary-button href="{{ route('admin.permissions.create') }}" icon="fas fa-plus" color="green">
                Nouvelle Permission
            </x-button.primary-button>    
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-4">Nom</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($permissions as $permission)
                        <tr class="border-t hover:bg-indigo-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $permission->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                                <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                    class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-700 transition">
                                    <i class="fas fa-pen-to-square"></i> Modifier
                                </a>

                                {{-- Use delete-button component --}}
                                <x-delete-button :route="route('admin.permissions.destroy', $permission->id)" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-500 py-8 italic">
                                Aucune permission trouvée.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $permissions->withQueryString()->links() }}
        </div>
    </div>
@endsection
