@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $product->title }}</h1>
    </x-slot>

    <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 max-w-4xl mx-auto mt-8">

        <!-- Product Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 pb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $product->title }}</h2>
                <p class="text-sm text-gray-500">ID: {{ $product->id }}</p>
            </div>
            <span
                class="{{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                       px-3 py-1 rounded-full text-xs font-medium mt-2 sm:mt-0">
                {{ $product->stock }} {{ Str::plural('item', $product->stock) }}
            </span>
        </div>

        <!-- Product Content -->
        <div class="space-y-6">
            @php
                // Determine main image once
                $mainImage = $product->images->firstWhere('is_primary', 1) ?? $product->images->first();
            @endphp

            <!-- Image Section -->
            <div class="flex flex-col sm:flex-row gap-6">
                <!-- Main Image -->
                <figure class="w-full max-w-sm">
                    @if ($mainImage)
                        <img src="{{ asset('storage/' . $mainImage->image_path) }}"
                             alt="{{ $product->title }}"
                             class="w-full h-64 object-cover rounded-lg shadow-sm">
                    @else
                        <div class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                            <i class="fas fa-image text-3xl"></i>
                            <span class="ml-2">No Image</span>
                        </div>
                    @endif
                </figure>

                <!-- Additional Images Gallery -->
                <div class="flex-1 overflow-x-auto py-2">
                    <h3 class="mb-3 text-lg font-semibold text-gray-700">Additional Images</h3>
                    <div class="flex space-x-4">
                        @forelse ($product->images->where('id', '!=', optional($mainImage)->id) as $image)
                            <figure class="flex-shrink-0 w-48 h-48 rounded-lg overflow-hidden shadow-sm border border-gray-200">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $product->title }}"
                                     class="w-full h-full object-cover">
                            </figure>
                        @empty
                            <p class="text-gray-500">No additional images available.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Product Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Price</dt>
                        <dd class="text-gray-800">${{ number_format($product->price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Category</dt>
                        <dd>
                            <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs">
                                {{ $product->category->name ?? 'N/A' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Created</dt>
                        <dd class="text-gray-800">{{ $product->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Updated</dt>
                        <dd class="text-gray-800">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Description -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Description</h3>
                <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-col sm:flex-row justify-end gap-4">
            <x-button.link :href="route('admin.products.edit', $product->id)" color="yellow">
                <i class="fas fa-pen-to-square"></i> Edit Product
            </x-button.link>

            <x-confirmation-modal
                :id="$product->id"
                :route="route('admin.products.destroy', $product->id)"
                title="Delete Product"
                message="Are you sure you want to delete '{{ $product->title }}'? This action cannot be undone."
                confirmText="Delete"
                buttonClass="inline-flex items-center gap-2 bg-red-600 text-white px-5 py-2.5 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                <i class="fas fa-trash"></i> Delete Product
            </x-confirmation-modal>

            <x-button.link :href="route('admin.products.index')" color="gray">
                <i class="fas fa-arrow-left"></i> Back to Products
            </x-button.link>
        </div>
    </div>
@endsection
