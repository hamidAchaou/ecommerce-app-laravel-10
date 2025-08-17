@extends('layouts.app')

@section('title', 'Welcome to ' . config('app.name'))

@section('content')

    {{-- Hero Section --}}
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 lg:flex lg:items-center lg:gap-12">
            
            {{-- Left Content --}}
            <div class="lg:w-1/2">
                <h1 class="text-4xl font-extrabold sm:text-5xl leading-tight">
                    Traditional Industry in Morocco –
                    <span class="text-red-600">{{ config('app.name') }}</span>
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Discover the rich heritage of Moroccan craftsmanship, from pottery and weaving 
                    to jewelry, woodwork, and more.
                </p>
                <div class="mt-6 flex gap-4">
                    <x-frontend.button.button-primary href="{{ route('products.index') }}">
                        Explore
                    </x-frontend.button.button-primary>
                    <x-frontend.button.button-secondary href="{{ route('about') }}">
                        Learn More
                    </x-frontend.button.button-secondary>
                </div>
            </div>
    
            {{-- Right Grid Images --}}
            <div class="lg:w-1/2 mt-8 lg:mt-0 grid grid-cols-2 gap-4">
                <img src="{{ asset('assets/images/hero-section.webp') }}" 
                     alt="Moroccan pottery and ceramics" 
                     loading="lazy" 
                     class="rounded-lg shadow-md object-cover w-full h-40 sm:h-48 md:h-56">
    
                <img src="{{ asset('assets/images/hero-section-1.webp') }}" 
                     alt="Traditional Moroccan weaving" 
                     loading="lazy" 
                     class="rounded-lg shadow-md object-cover w-full h-40 sm:h-48 md:h-56">
    
                <img src="{{ asset('assets/images/hero-section-2.webp') }}" 
                     alt="Handcrafted Moroccan leatherwork" 
                     loading="lazy" 
                     class="rounded-lg shadow-md object-cover w-full h-40 sm:h-48 md:h-56">
    
                <img src="{{ asset('assets/images/hero-section-3.webp') }}" 
                     alt="Decorative Moroccan woodwork" 
                     loading="lazy" 
                     class="rounded-lg shadow-md object-cover w-full h-40 sm:h-48 md:h-56">
            </div>
        </div>
    </section>
    

    {{-- Categories --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <x-frontend.section-header 
                title="Featured Products" 
                subtitle="Check out our most popular products" 
            />
    
            <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <x-frontend.category.card :category="$category" />
                @endforeach
            </div>
        </div>
    </section>
    
{{-- Featured Products --}}
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
        <x-frontend.section-header 
            title="Featured Products" 
            subtitle="Check out our most popular products" 
        />

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featuredProducts as $product)
                <x-frontend.product.card :product="$product" />
            @endforeach
        </div>
    </div>
</section>


@endsection
