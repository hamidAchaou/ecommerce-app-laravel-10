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
                <x-text-input id="title" name="title" type="text" :value="old('title', $product->title ?? '')" required
                    autofocus
                    class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="Product title" />
                <x-input-error :messages="$errors->get('title')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Description -->
            <div>
                <x-input-label for="description" :value="__('Description')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <textarea name="description" id="description" rows="4" required
                    class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="Write a short product description...">{{ old('description', $product->description ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Price -->
            <div>
                <x-input-label for="price" :value="__('Price')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input id="price" name="price" type="number" step="0.01" min="0" required
                    :value="old('price', $product->price ?? '')"
                    class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="0.00" />
                <x-input-error :messages="$errors->get('price')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Product Images -->
            <div>
                <x-input-label for="images" :value="__('Product Images')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/jpg,image/gif"
                    class="block w-full text-sm text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-700 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                @if ($errors->has('images.*'))
                    <div class="mt-1 text-sm text-red-600 dark:text-red-400">
                        @foreach ($errors->get('images.*') as $messages)
                            @foreach ($messages as $message)
                                <p>{{ $message }}</p>
                            @endforeach
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Stock -->
            <div>
                <x-input-label for="stock" :value="__('Stock')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input id="stock" name="stock" type="number" min="0" required
                    :value="old('stock', $product->stock ?? '')"
                    class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="0" />
                <x-input-error :messages="$errors->get('stock')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Category -->
            <div>
                <x-input-label for="category_id" :value="__('Category')"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <select name="category_id" id="category_id" required
                    class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
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
@endsection