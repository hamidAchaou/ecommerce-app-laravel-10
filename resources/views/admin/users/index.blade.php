@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-7xl mx-auto mt-8">

        {{-- 🔍 Search + Add --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <x-search-form 
                route="admin.users.index" 
                placeholder="Rechercher un utilisateur..." 
                class="w-full md:w-auto"
            />
            
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500">
                <i class="fas fa-user-plus"></i>
                Ajouter un utilisateur
            </a>
        </div>

        {{-- 📋 Users Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-4">Nom</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Rôles</th>
                        <th class="px-6 py-4">Permissions</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700">
                    @forelse ($users as $user)
                        <tr class="border-t hover:bg-indigo-50 transition">
                            {{-- Nom --}}
                            <td class="px-6 py-4">{{ $user->name }}</td>

                            {{-- Email --}}
                            <td class="px-6 py-4">{{ $user->email }}</td>

                            {{-- Rôles --}}
                            <td class="px-6 py-4">
                                @foreach ($user->roles as $role)
                                    <span class="inline-flex items-center gap-1 text-xs bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full mr-1 mb-1">
                                        <i class="fas fa-user-shield text-indigo-600"></i> {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>

                            {{-- Permissions --}}
                            <td class="px-6 py-4 text-sm">
                                <div class="mb-1">
                                    <span class="font-semibold text-gray-700">Directes:</span><br>
                                    @forelse($user->permissions as $permission)
                                        <span class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full mr-1 mb-1">
                                            <i class="fas fa-key text-green-600"></i> {{ $permission->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 italic">Aucune</span>
                                    @endforelse
                                </div>
                                <div class="mt-2">
                                    <span class="font-semibold text-gray-700">Via rôles:</span><br>
                                    @foreach ($user->getPermissionsViaRoles() as $permission)
                                        <span class="inline-flex items-center gap-1 text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full mr-1 mb-1">
                                            <i class="fas fa-link text-blue-600"></i> {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="text-yellow-600 hover:text-yellow-700 inline-flex items-center gap-1 transition">
                                    <i class="fas fa-pen-to-square"></i> Modifier
                                </a>

                                <x-delete-button :route="route('admin.users.destroy', $user)" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-6 italic">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $users->links() }}
        </div>
    </div>
@endsection
