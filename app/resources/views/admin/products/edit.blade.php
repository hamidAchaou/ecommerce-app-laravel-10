@extends('layouts.app')
@section('title', 'Edit Product')
@section('content')
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Product</h1>
    </x-slot>
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto mt-8">
        @include('admin.products.form', [
            'categories' => $categories,
            'action' => route('admin.products.update', $product->id),
            'method' => 'PUT',
            'product' => $product
        ])
    </div>
@endsection