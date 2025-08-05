@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-8 max-w-7xl mx-auto mt-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-users mr-2 text-indigo-600"></i> Liste des Utilisateurs
        </h1>
        {{-- 🔁 Replaced Modal Trigger with Link --}}
        <a 
            href="{{ route('admin.users.create') }}" 
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
            <i class="fas fa-user-plus mr-2"></i> Ajouter un utilisateur
        </a>
    </div>

    {{-- User Table --}}
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
                @foreach($users as $user)
                <tr class="border-t hover:bg-indigo-50 transition">
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                            <span class="inline-flex items-center gap-1 text-xs bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full mr-1 mb-1">
                                <i class="fas fa-user-shield text-indigo-600"></i> {{ $role->name }}
                            </span>
                        @endforeach
                    </td>
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
                            @foreach($user->getPermissionsViaRoles() as $permission)
                                <span class="inline-flex items-center gap-1 text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full mr-1 mb-1">
                                    <i class="fas fa-link text-blue-600"></i> {{ $permission->name }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center space-x-2">
                        {{-- Edit Button --}}
                        <button 
                            onclick="document.getElementById('edit-user-{{ $user->id }}').classList.remove('hidden')" 
                            class="text-yellow-600 hover:text-yellow-700"
                        >
                            <i class="fas fa-pen-to-square"></i> Modifier
                        </button>

                        {{-- Delete Button --}}
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-700">
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- Edit Modal --}}
                <div id="edit-user-{{ $user->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl relative">
                        <button 
                            onclick="document.getElementById('edit-user-{{ $user->id }}').classList.add('hidden')" 
                            class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold"
                        >
                            &times;
                        </button>

                        <h2 class="text-lg font-semibold mb-4">
                            <i class="fas fa-user-edit text-indigo-600 mr-2"></i>
                            Modifier Rôles et Permissions: <span class="text-indigo-800">{{ $user->name }}</span>
                        </h2>

                        <form method="POST" action="{{ route('admin.users.updateRolesPermissions', $user) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block font-medium mb-2">Rôles</label>
                                <select name="roles[]" multiple class="w-full border border-gray-300 rounded-md p-2" size="5">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" @if($user->roles->contains('name', $role->name)) selected @endif>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block font-medium mb-2">Permissions</label>
                                <select name="permissions[]" multiple class="w-full border border-gray-300 rounded-md p-2" size="5">
                                    @foreach($permissions as $permission)
                                        <option value="{{ $permission->name }}" @if($user->permissions->contains('name', $permission->name)) selected @endif>{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-end space-x-4">
                                <button 
                                    type="button" 
                                    onclick="document.getElementById('edit-user-{{ $user->id }}').classList.add('hidden')" 
                                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                >
                                    Annuler
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                                >
                                    <i class="fas fa-save mr-1"></i> Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $users->links() }}
    </div>
</div>
@endsection
