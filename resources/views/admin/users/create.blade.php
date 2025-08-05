@extends('layouts.app')

@section('title', 'Créer un Utilisateur')

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto mt-8">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-user-plus text-green-600"></i> Créer un nouvel utilisateur
        </h1>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block font-medium text-sm text-gray-700">Nom</label>
                <input id="name" name="name" type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    value="{{ old('name') }}" required autofocus>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                <input id="email" name="email" type="email"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    value="{{ old('email') }}" required>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block font-medium text-sm text-gray-700">Mot de passe</label>
                <input id="password" name="password" type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
            </div>
            {{-- Password Confirmation --}}
            <div>
                <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmer le mot de
                    passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required>
            </div>


            {{-- Roles --}}
            <div>
                <label class="block font-medium text-sm text-gray-700 mb-1">Rôles</label>
                <select name="roles[]" multiple class="w-full border border-gray-300 rounded-md p-2" size="5">
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @if (in_array($role->name, old('roles', []))) selected @endif>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <i class="fas fa-save mr-1"></i> Créer
                </button>
            </div>
        </form>
    </div>
@endsection
