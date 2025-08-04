@extends('layouts.app')

@section('title', 'Modifier un Rôle')

@section('content')
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800">Modifier le rôle "{{ $role->name }}"</h1>
    </x-slot>

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto mt-8">
        @include('admin.roles.form', [
            'permissions' => $permissions,
            'action' => route('admin.roles.update', $role->id),
            'method' => 'PUT',
            'role' => $role
        ])
    </div>
@endsection
