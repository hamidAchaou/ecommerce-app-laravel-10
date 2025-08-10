@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Category</h1>
    </x-slot>

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto mt-8">
        @include('admin.categories.form', [
            'categories' => $categories,
            'action' => route('admin.categories.update', $category->id),
            'method' => 'PUT',
            'category' => $category
        ])
    </div>
@endsection
