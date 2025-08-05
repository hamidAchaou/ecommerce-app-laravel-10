@extends('layouts.app')

@section('title', 'Modifier un Utilisateur')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto mt-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fas fa-user-edit text-yellow-600"></i> Modifier l'utilisateur : <span class="font-semibold">{{ $user->name }}</span>
    </h1>

    @if ($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.users.form')
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </a>
            <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                <i class="fas fa-save mr-1"></i> Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
