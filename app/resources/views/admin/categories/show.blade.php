@extends('layouts.app')

@section('title', 'Category Details')

@section('content')
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $category->name }}</h1>
    </x-slot>

    <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 max-w-4xl mx-auto mt-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 pb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $category->name }}</h2>
                <p class="text-sm text-gray-500">ID: {{ $category->id }}</p>
            </div>

            <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-xs font-medium mt-2 sm:mt-0">
                {{ $category->type ?? 'N/A' }}
            </span>
        </div>

        <!-- Details -->
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Category Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Parent Category</dt>
                        <dd>
                            @if ($category->parent)
                                <a href="{{ route('admin.categories.show', $category->parent->id) }}"
                                   class="text-indigo-600 hover:underline">
                                    {{ $category->parent->name }}
                                </a>
                            @else
                                <span class="text-gray-500">No parent</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Subcategories</dt>
                        <dd>
                            @if ($category->subcategories->count())
                                <ul class="list-disc list-inside text-gray-800">
                                    @foreach ($category->subcategories as $sub)
                                        <li>
                                            <a href="{{ route('admin.categories.show', $sub->id) }}"
                                               class="text-indigo-600 hover:underline">
                                                {{ $sub->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500">No subcategories</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Created</dt>
                        <dd class="text-gray-800">{{ $category->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Updated</dt>
                        <dd class="text-gray-800">{{ $category->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-col sm:flex-row justify-end gap-4">
            <x-button.link :href="route('admin.categories.edit', $category->id)" color="yellow">
                <i class="fas fa-pen-to-square"></i> Edit Category
            </x-button.link>

            <x-confirmation-modal
                :id="$category->id"
                :route="route('admin.categories.destroy', $category->id)"
                title="Delete Category"
                message="Are you sure you want to delete '{{ $category->name }}'? This action cannot be undone."
                confirmText="Delete"
                buttonClass="inline-flex items-center gap-2 bg-red-600 text-white px-5 py-2.5 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                <i class="fas fa-trash"></i> Delete Category
            </x-confirmation-modal>

            <x-button.link :href="route('admin.categories.index')" color="gray">
                <i class="fas fa-arrow-left"></i> Back to Categories
            </x-button.link>
        </div>
    </div>
@endsection
