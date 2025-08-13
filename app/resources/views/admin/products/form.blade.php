@extends('layouts.app')

@section('content')
    <div
        class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 sm:p-8 overflow-hidden transform transition-all duration-300 hover:shadow-2xl max-w-2xl mx-auto">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6">
            {{ $product ? 'Edit Product' : 'Create Product' }}
        </h2>

        @if ($errors->has('images.*'))
            <div class="mt-1 text-sm text-red-600 dark:text-red-400">
                @foreach ($errors->get('images.*') as $messages)
                    @foreach ($messages as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                @endforeach
            </div>
        @endif

        <form action="{{ $action }}" method="POST" class="space-y-6" enctype="multipart/form-data" novalidate>
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <!-- Title -->
            <div>
                <x-input-label for="title" :value="__('Title')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input id="title" name="title" type="text" :value="old('title', $product->title ?? '')" required autofocus
                    class="appearance-none rounded-md w-full px-4 py-3 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="Product title" />
                <x-input-error :messages="$errors->get('title')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Description -->
            <div>
                <x-input-label for="description" :value="__('Description')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <textarea name="description" id="description" rows="4" required
                    class="appearance-none rounded-md w-full px-4 py-3 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="Write a short product description...">{{ old('description', $product->description ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Price -->
            <div>
                <x-input-label for="price" :value="__('Price')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input id="price" name="price" type="number" step="0.01" min="0" required
                    :value="old('price', $product->price ?? '')"
                    class="appearance-none rounded-md w-full px-4 py-3 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="0.00" />
                <x-input-error :messages="$errors->get('price')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Product Images -->
            <div>
                <x-input-label for="images" :value="__('Product Images')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />

                {{-- Show existing images --}}
                @if (!empty($product) && $product->images->count())
                    <div class="mb-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach ($product->images as $image)
                            <div class="relative border rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition h-32 sm:h-40"
                                id="image-card-{{ $image->id }}">
                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank"
                                    class="block w-full h-full">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ $product->title ?? 'Product Image' }}"
                                        class="w-full h-full object-cover hover:scale-105 transition duration-300" />
                                </a>
                                <button type="button"
                                    class="absolute top-2 right-2 z-20 bg-red-600 bg-opacity-80 text-white rounded-full p-1.5 hover:bg-opacity-100 hover:scale-110 transition focus:outline-none focus:ring-2 focus:ring-red-500 shadow delete-image-btn"
                                    data-product-id="{{ $product->id }}"
                                    data-image-id="{{ $image->id }}"
                                    data-url="{{ route('admin.products.images.destroy', ['product' => $product->id, 'image' => $image->id]) }}">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload new images --}}
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="block w-full text-sm text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-700 rounded-md cursor-pointer focus:ring-2 focus:ring-indigo-500" />
            </div>

            <!-- Stock -->
            <div>
                <x-input-label for="stock" :value="__('Stock')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input id="stock" name="stock" type="number" min="0" required :value="old('stock', $product->stock ?? '')"
                    class="appearance-none rounded-md w-full px-4 py-3 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="0" />
                <x-input-error :messages="$errors->get('stock')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Category -->
            <div>
                <x-input-label for="category_id" :value="__('Category')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <select name="category_id" id="category_id" required
                    class="appearance-none rounded-md w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <x-primary-button
                    class="px-6 py-3 text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 rounded-md shadow-md transition font-semibold">
                    {{ $product ? 'Update Product' : 'Create Product' }}
                </x-primary-button>
            </div>
        </form>
    </div>

    {{-- Improved AJAX delete script --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
            
            if (!csrfToken) {
                console.error('CSRF token not found. Make sure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your layout.');
                return;
            }

            // Handle delete button clicks
            document.querySelectorAll(".delete-image-btn").forEach(button => {
                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    
                    if (!confirm("Are you sure you want to delete this image?")) {
                        return;
                    }

                    const productId = this.dataset.productId;
                    const imageId = this.dataset.imageId;
                    const deleteUrl = this.dataset.url;
                    const imageCard = this.closest(".relative");

                    console.log('Deleting image:', { productId, imageId, deleteUrl });

                    // Show loading state
                    this.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
                    this.disabled = true;

                    fetch(deleteUrl, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json",
                            "Content-Type": "application/json"
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.error || `HTTP error! status: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Success:', data);
                        imageCard.remove();
                        alert(data.message || "Image deleted successfully!");
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(`Error: ${error.message}`);
                        
                        // Reset button on error
                        this.innerHTML = '<i class="fas fa-times text-sm"></i>';
                        this.disabled = false;
                    });
                });
            });
        });
    </script>
@endsection