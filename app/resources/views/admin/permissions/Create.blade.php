@extends('layouts.app')

@section('title', 'Créer une Permission')

@section('content')
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800">Créer une permission</h1>
    </x-slot>

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto mt-8">
        @include('admin.permissions.form', [
            'action' => route('admin.permissions.store'),
            'method' => 'POST',
            'permission' => null
        ])
    </div>
@endsection
