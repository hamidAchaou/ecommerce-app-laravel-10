@extends('layouts.app')

@section('title', 'Product Dashboard')

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-7xl mx-auto mt-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <x-search-form route="admin.products.index" placeholder="Search products by title..." />

            <div class="flex flex-wrap gap-3">
                <x-button.primary-button href="{{ route('admin.products.import.form') }}" icon="fas fa-file-import" color="blue">
                    Import Products
                </x-button.primary-button>

                <x-button.primary-button href="{{ route('admin.products.export') }}" icon="fas fa-file-export" color="gray">
                    Export Products
                </x-button.primary-button>

                <x-button.primary-button href="{{ route('admin.products.create') }}" icon="fas fa-plus" color="green">
                    Add Product
                </x-button.primary-button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4 text-center">Price</th>
                        <th class="px-6 py-4 text-center">Stock</th>
                        <th class="px-6 py-4 text-center">Category</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($products as $product)
                        <tr class="border-t hover:bg-indigo-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $product->title }}</td>
                            <td class="px-6 py-4 text-center">{{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="{{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-3 py-1 rounded-full text-xs">
                                    {{ $product->stock }} {{ $product->stock === 1 ? 'item' : 'items' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                                <a href="{{ route('admin.products.show', $product->id) }}"
                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 transition">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-700 transition">
                                    <i class="fas fa-pen-to-square"></i> Edit
                                </a>
                                <x-delete-button :route="route('admin.products.destroy', $product->id)" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-8 italic">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
@endsection
