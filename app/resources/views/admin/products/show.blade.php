@extends('layouts.app')

@section('title', $product->title . ' - Product Details')

@section('content')
<div class="max-w-6xl mx-auto mt-10 p-4 sm:p-8 bg-white rounded-xl shadow-lg">

    <!-- Product Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-200 pb-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">ID: {{ $product->id }}</p>
        </div>
        <span class="{{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                    px-4 py-1 rounded-full text-sm font-semibold mt-3 sm:mt-0">
            {{ $product->stock }} {{ Str::plural('item', $product->stock) }}
        </span>
    </div>

    @php
        $allImages = $product->images;
    @endphp

    <!-- Product Gallery & Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Gallery -->
        <div x-data="{ open: false, activeImage: 0, images: {{ $allImages->pluck('image_path')->toJson() }} }" class="space-y-4">
            <figure class="relative w-full rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                <img :src="'{{ asset('storage') }}/' + images[activeImage]"
                     alt="{{ $product->title }}"
                     class="w-full h-[500px] object-contain cursor-pointer transition-transform hover:scale-105"
                     loading="lazy"
                     @click="open = true">
                
                <!-- Navigation -->
                <button @click="activeImage = (activeImage - 1 + images.length) % images.length"
                        class="absolute top-1/2 left-2 -translate-y-1/2 bg-black/40 text-white rounded-full p-2 hover:bg-black/60">
                    &#10094;
                </button>
                <button @click="activeImage = (activeImage + 1) % images.length"
                        class="absolute top-1/2 right-2 -translate-y-1/2 bg-black/40 text-white rounded-full p-2 hover:bg-black/60">
                    &#10095;
                </button>
            </figure>

            <!-- Thumbnails -->
            <div class="flex space-x-2 overflow-x-auto mt-2">
                <template x-for="(img, index) in images" :key="index">
                    <img :src="'{{ asset('storage') }}/' + img"
                         :alt="'{{ $product->title }} - ' + (index + 1)"
                         class="w-20 h-20 object-cover rounded-md border cursor-pointer hover:ring-2 hover:ring-indigo-500"
                         @click="activeImage = index">
                </template>
            </div>

            <!-- Modal for larger view -->
            <div x-show="open" x-transition class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
                 @keydown.escape.window="open = false">
                <button @click="open = false" class="absolute top-5 right-5 text-white text-3xl hover:text-gray-300">&times;</button>
                <img :src="'{{ asset('storage') }}/' + images[activeImage]"
                     alt="{{ $product->title }}"
                     class="max-h-[80vh] object-contain rounded-lg shadow-lg">
            </div>
        </div>

        <!-- Product Details -->
        <div class="flex flex-col justify-between">
            <div class="space-y-4">
                <div>
                    <span class="text-xl font-bold text-indigo-600">${{ number_format($product->price, 2) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-700">Category:</span>
                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                        {{ $product->category->name ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-gray-500 text-sm">
                    <span>Created: {{ $product->created_at->format('M d, Y H:i') }}</span>
                    <span>|</span>
                    <span>Updated: {{ $product->updated_at->format('M d, Y H:i') }}</span>
                </div>

                <!-- Description -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Description</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-col sm:flex-row gap-4">
                <x-button.link :href="route('admin.products.edit', $product->id)" color="yellow">
                    <i class="fas fa-pen-to-square mr-2"></i> Edit Product
                </x-button.link>

                <x-confirmation-modal
                    :id="$product->id"
                    :route="route('admin.products.destroy', $product->id)"
                    title="Delete Product"
                    message="Are you sure you want to delete '{{ $product->title }}'? This action cannot be undone."
                    confirmText="Delete"
                    buttonClass="inline-flex items-center gap-2 bg-red-600 text-white px-5 py-2.5 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                    <i class="fas fa-trash mr-2"></i> Delete Product
                </x-confirmation-modal>

                <x-button.link :href="route('admin.products.index')" color="gray">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Products
                </x-button.link>
            </div>
        </div>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection
