@extends('layouts.app')

@section('content')

<div
    class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 sm:p-8 overflow-hidden transform transition-all duration-300 hover:shadow-2xl max-w-2xl mx-auto">

    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6">
        {{ $category ? 'Edit Category' : 'Create Category' }}
    </h2>

    <form action="{{ $action }}" method="POST" class="space-y-6" novalidate>
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Category Name')"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input id="name" name="name" type="text" 
                :value="old('name', $category->name ?? '')" required autofocus
                class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition"
                placeholder="Enter category name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Parent Category with Subcategories (Indented) -->
        <div>
            <x-input-label for="parent_id" :value="__('Parent Category')"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <select name="parent_id" id="parent_id"
                class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="">-- No Parent --</option>

                @php
                    /**
                     * Recursive function to render categories with indentation.
                     * @param \Illuminate\Support\Collection $categories
                     * @param int $level
                     * @param int|null $selectedId
                     */
                    function renderCategoryOptions($categories, $level = 0, $selectedId = null) {
                        foreach ($categories as $cat) {
                            // Indent with &nbsp; for better UI
                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
                            $isSelected = old('parent_id', request()->old('parent_id') ?? null) == $cat->id || $selectedId == $cat->id;
                            echo '<option value="' . $cat->id . '"' . ($isSelected ? ' selected' : '') . '>' . $indent . e($cat->name) . '</option>';

                            if ($cat->subcategories && $cat->subcategories->count() > 0) {
                                renderCategoryOptions($cat->subcategories, $level + 1, $selectedId);
                            }
                        }
                    }
                @endphp

                {{-- Render categories recursively --}}
                @php
                    renderCategoryOptions($categories, 0, $category->parent_id ?? null);
                @endphp
            </select>
            <x-input-error :messages="$errors->get('parent_id')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Type -->
        <div>
            <x-input-label for="type" :value="__('Category Type')"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input id="type" name="type" type="text" 
                :value="old('type', $category->type ?? '')"
                class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition"
                placeholder="Optional category type" />
            <x-input-error :messages="$errors->get('type')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <x-primary-button
                class="px-6 py-3 text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 rounded-md shadow-md transition font-semibold">
                {{ $category ? 'Update Category' : 'Create Category' }}
            </x-primary-button>
        </div>
    </form>
</div>

@endsection
