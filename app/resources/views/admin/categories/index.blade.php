@extends('layouts.app')

@section('title', 'Category Dashboard')

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-7xl mx-auto mt-8">

        <!-- Header and Add Category Button -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
            <x-search-form 
                route="admin.categories.index" 
                placeholder="Search categories by name..."
                :searchValue="request('search')"  {{-- Pass current search term if any --}}
            />

            <x-button.primary-button href="{{ route('admin.categories.create') }}" icon="fas fa-plus" color="green">
                Add Category
            </x-button.primary-button>    
        </div>

        <!-- Categories Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4 text-center">Parent Category</th>
                        <th class="px-6 py-4 text-center">Type</th>
                        <th class="px-6 py-4 text-center">Subcategories</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($categories as $category)
                        <tr class="border-t hover:bg-indigo-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-center">{{ $category->parent ? $category->parent->name : '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ ucfirst($category->type ?? 'N/A') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $category->subcategories->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                                <a href="{{ route('admin.categories.show', $category->id) }}" 
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 transition">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                   class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-700 transition">
                                    <i class="fas fa-pen-to-square"></i> Edit
                                </a>
                                <x-delete-button :route="route('admin.categories.destroy', $category->id)" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-8 italic">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $categories->withQueryString()->links() }}
        </div>
    </div>
@endsection
