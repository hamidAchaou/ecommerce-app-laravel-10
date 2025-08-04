@extends('layouts.app')

@section('title', 'Gestion des Rôles')

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-7xl mx-auto mt-8">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
            <form method="GET" action="{{ route('admin.roles.index') }}" class="flex w-full md:w-auto">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Rechercher un rôle..." 
                    class="flex-grow border border-gray-300 rounded-l-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                />
                <button 
                    type="submit" 
                    class="bg-indigo-600 text-white px-5 py-3 rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                >
                    Rechercher
                </button>
            </form>

            <a href="{{ route('admin.roles.create') }}" 
                class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                <i class="fas fa-plus"></i>
                Nouveau Rôle
            </a>
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
