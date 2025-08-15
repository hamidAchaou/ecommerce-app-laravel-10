@extends('layouts.app')

@section('title', 'Welcome to ' . config('app.name'))

@section('content')

    {{-- Hero Section --}}
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 lg:flex lg:items-center lg:gap-12">
            <div class="lg:w-1/2">
                <h1 class="text-4xl font-extrabold sm:text-5xl">
                    Welcome to <span class="text-red-600">{{ config('app.name') }}</span>
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Discover amazing products at unbeatable prices.
                </p>
                <div class="mt-6 flex gap-4">
                    <x-frontend.button.button-primary href="{{ route('products.index') }}">
                        Shop Now
                    </x-frontend.button.button-primary>
                    <x-frontend.button.button-secondary href="{{ route('about') }}">
                        Learn More
                    </x-frontend.button.button-secondary>                    

                </div>
            </div>
            <div class="lg:w-1/2 mt-8 lg:mt-0">
                {{-- <img src="{{ asset('images/hero.jpg') }}" alt="Hero" class="rounded-lg shadow-md"> --}}
                <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="Hero image">
            
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <x-frontend.section-header title="Featured Products" subtitle="Check out our most popular products" />
                        <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    {{-- <x-category.card :category="$category" /> --}}
                    <x-frontend.category.card :category="$category" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured Products --}}
    <section class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4">
            <x-frontend.section-header title="Featured Products" subtitle="Check out our most popular products" />
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- @dd($featuredProducts) --}}

                @foreach ($featuredProducts as $product)
                    <x-frontend.product.card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>

@endsection
