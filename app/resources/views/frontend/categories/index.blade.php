@extends('layouts.app')

@section('title', 'Categories | ' . config('app.name'))
@section('meta_description', 'Browse Moroccan craft categories including textiles, leatherwork, pottery, jewelry, and
    woodwork — preserving Morocco’s cultural heritage.')

@section('content')

    {{-- Hero --}}
    <section class="relative bg-gradient-to-r from-red-50 via-white to-red-50 py-12">
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900">
                Explore <span class="text-red-600">Categories</span>
            </h1>
            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                Discover Morocco’s timeless traditions through our artisan categories — from weaving and pottery to jewelry
                and woodwork.
            </p>
        </div>
        <div class="absolute inset-0 opacity-10 bg-[url('/assets/images/pattern-morocco.webp')] bg-repeat"></div>
    </section>

    {{-- Categories Section --}}
    <section class="bg-morocco-ivory py-16">
        <div class="max-w-7xl mx-auto px-6">
            {{-- Categories --}}
            <div class="space-y-16" id="categoriesWrapper">
                @foreach ($categories as $index => $category)
                    <div class="category-item flex flex-col {{ $index % 2 == 0 ? 'lg:flex-row' : 'lg:flex-row-reverse' }} lg:items-center gap-8"
                        data-name="{{ strtolower($category->name) }} {{ strtolower($category->description) }}">

                        {{-- Category Image --}}
                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" loading="lazy"
                            class="rounded-lg shadow-lg w-full lg:w-1/2 h-64 object-cover border-2 border-morocco-{{ $index % 2 == 0 ? 'blue' : 'green' }}">

                        {{-- Category Content --}}
                        <div class="lg:w-1/2">
                            <h3 class="text-2xl font-semibold text-morocco-{{ $index % 2 == 0 ? 'blue' : 'green' }}">
                                {{ $category->name }}
                            </h3>
                            <p class="mt-2 text-gray-700">
                                {{ $category->description }}
                            </p>
                            <a href="{{ route('categories.show', $category->id) }}"
                                class="mt-4 inline-block px-5 py-2 rounded-lg bg-morocco-red text-white font-semibold shadow hover:bg-red-700 transition">
                                Explore
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
