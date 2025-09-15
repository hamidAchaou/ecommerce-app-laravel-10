@extends('layouts.app')

@section('title', 'Traditional Moroccan Crafts – ' . config('app.name'))

@section('meta')
    <meta name="description"
        content="Discover authentic Moroccan crafts: pottery, weaving, jewelry, woodwork, and more. Explore and shop traditional Moroccan heritage products.">
    <meta name="keywords"
        content="Moroccan crafts, Moroccan pottery, Moroccan weaving, Moroccan jewelry, traditional crafts Morocco">
@endsection

@section('content')
    <main>

        {{-- Hero Section --}}
        <section class="relative bg-gradient-to-r from-red-50 via-white to-red-50 py-12">
            <div class="max-w-7xl mx-auto px-4 lg:flex lg:items-center lg:gap-12 relative z-10">

                {{-- Left Content --}}
                <div class="lg:w-1/2">
                    <h1 class="text-4xl font-extrabold sm:text-5xl leading-tight">
                        Explore Moroccan Traditional Crafts –
                        <span class="text-red-600">{{ config('app.name') }}</span>
                    </h1>
                    <p class="mt-4 text-lg text-gray-600">
                        Discover the rich heritage of Moroccan craftsmanship: pottery, weaving, jewelry, woodwork, and more.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-4">
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
                    @php
                        $heroImages = [
                            ['src' => 'hero-section.webp', 'alt' => 'Moroccan pottery and ceramics'],
                            ['src' => 'hero-section-1.webp', 'alt' => 'Traditional Moroccan weaving'],
                            ['src' => 'hero-section-2.webp', 'alt' => 'Handcrafted Moroccan leatherwork'],
                            ['src' => 'hero-section-3.webp', 'alt' => 'Decorative Moroccan woodwork'],
                        ];
                    @endphp

                    @foreach ($heroImages as $image)
                        <img src="{{ asset('assets/images/' . $image['src']) }}" alt="{{ $image['alt'] }}" loading="lazy"
                            class="rounded-xl shadow-lg object-cover w-full h-40 sm:h-48 md:h-56">
                    @endforeach
                </div>
            </div>

            {{-- Decorative Pattern --}}
            <div class="absolute inset-0 opacity-10 bg-[url('assets/images/pattern-morocco.webp')] bg-repeat"></div>
        </section>

        {{-- Categories --}}
        <section class="py-12 bg-gradient-to-b from-white via-gray-50 to-white">
            <div class="max-w-7xl mx-auto px-4">
                <x-frontend.section-header title="Featured Categories"
                    subtitle="Explore our traditional Moroccan collections" />

                <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <x-frontend.category.card :category="$category" />
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Featured Products --}}
        <section class="bg-gradient-to-r from-gray-50 via-white to-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4">
                <x-frontend.section-header title="Featured Products" subtitle="Check out our most popular products" />

                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featuredProducts as $product)
                        <x-frontend.product.card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Contact Section --}}
        <section id="contact" class="relative bg-gradient-to-b from-white via-red-50 to-white py-16">
            <div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-10">

                {{-- Section Header --}}
                <x-frontend.section-header title="Get in Touch"
                    subtitle="Have questions about our Moroccan crafts? We’d love to hear from you!" />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    {{-- Contact Form --}}
                    <x-frontend.form.contact-form />

                    {{-- Contact Info + Map --}}
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Our Office</h3>
                            <p class="mt-2 text-gray-600">
                                123 Medina Street, Marrakech, Morocco
                            </p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Contact</h3>
                            <p class="mt-2 text-gray-600">
                                Email: <a href="mailto:info@moroccancrafts.com"
                                    class="text-red-600 hover:underline">info@moroccancrafts.com</a><br>
                                Phone: <a href="tel:+212600000000" class="text-red-600 hover:underline">+212 600 000 000</a>
                            </p>
                        </div>

                        {{-- Lazy load map for performance --}}
                        <div class="rounded-xl overflow-hidden shadow-md">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3329.487!2d-7.9811!3d31.6295"
                                width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Decorative Pattern --}}
            <div class="absolute inset-0 opacity-5 bg-[url('assets/images/pattern-morocco.webp')] bg-repeat"></div>
        </section>

    </main>
@endsection
