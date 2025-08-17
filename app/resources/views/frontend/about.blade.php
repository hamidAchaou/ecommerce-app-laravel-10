@extends('layouts.app')

@section('title', 'About Us | ' . config('app.name'))
@section('meta_description', 'Discover the story behind ' . config('app.name') . ' — preserving Moroccan traditional crafts including pottery, weaving, woodwork, jewelry, and leatherwork.')

@section('content')

    {{-- Hero Section --}}
    <section class="relative bg-morocco-ivory">
        <div class="max-w-7xl mx-auto px-6 py-16 lg:flex lg:items-center lg:gap-12">
            {{-- Left Content --}}
            <div class="lg:w-1/2 animate-fadeIn">
                <h1 class="text-4xl font-extrabold text-morocco-red sm:text-5xl leading-tight">
                    About <span class="text-morocco-blue">{{ config('app.name') }}</span>
                </h1>
                <p class="mt-4 text-lg text-gray-700">
                    We are dedicated to preserving Morocco’s timeless traditions through authentic handmade crafts — from pottery and weaving to intricate jewelry and woodwork. 
                </p>
                <p class="mt-2 text-lg text-gray-700">
                    Every product tells a story of heritage, craftsmanship, and Moroccan identity.
                </p>
                <div class="mt-6 flex gap-4">
                    <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-morocco-red text-white font-semibold shadow hover:bg-red-700 transition">
                        Explore Products
                    </a>
                    <a href="{{ route('contact') }}" class="px-6 py-3 rounded-xl border-2 border-morocco-blue text-morocco-blue font-semibold hover:bg-morocco-blue hover:text-white transition">
                        Contact Us
                    </a>
                </div>
            </div>

            {{-- Right Image --}}
            <div class="lg:w-1/2 mt-8 lg:mt-0">
                <img src="{{ asset('assets/images/moroccan-crafts_moroccan-tapestry.webp') }}" 
                     alt="Moroccan artisans working on traditional crafts" 
                     loading="lazy" 
                     class="rounded-xl shadow-lg object-cover w-full h-80 sm:h-96 border-4 border-morocco-yellow">
            </div>
        </div>
    </section>

    {{-- Mission Section --}}
    <section class="bg-morocco-blue py-16">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">Our Mission</h2>
            <p class="mt-4 max-w-3xl mx-auto text-lg text-morocco-ivory">
                To connect Moroccan artisans with the world by showcasing authentic handmade crafts that carry the spirit of tradition and cultural richness.
            </p>
        </div>
    </section>

    {{-- Values Grid --}}
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-3">
                
                <div class="card p-6 text-center shadow-lg rounded-xl border-t-4 border-morocco-red">
                    <i class="fas fa-landmark text-4xl text-morocco-red mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900">Preserving Heritage</h3>
                    <p class="mt-2 text-gray-600">Every craft embodies centuries of Moroccan artistry, passed down from generation to generation.</p>
                </div>

                <div class="card p-6 text-center shadow-lg rounded-xl border-t-4 border-morocco-green">
                    <i class="fas fa-hands text-4xl text-morocco-green mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900">Authentic Craftsmanship</h3>
                    <p class="mt-2 text-gray-600">Our artisans use traditional methods to create unique, one-of-a-kind pieces.</p>
                </div>

                <div class="card p-6 text-center shadow-lg rounded-xl border-t-4 border-morocco-yellow">
                    <i class="fas fa-leaf text-4xl text-morocco-yellow mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900">Sustainable Practices</h3>
                    <p class="mt-2 text-gray-600">We support eco-friendly production and empower local communities.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Storytelling Timeline --}}
    <section class="bg-morocco-ivory py-16">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl sm:text-4xl font-bold text-center text-morocco-red mb-12">Our Story</h2>
            
            <div class="space-y-12">
                <div class="flex flex-col lg:flex-row lg:items-center gap-8">
                    <img src="{{ asset('assets/images/Whisk_1cff5806f3.webp') }}" alt="Moroccan pottery making" 
                         class="rounded-lg shadow-md w-full lg:w-1/2 h-64 object-cover border-2 border-morocco-blue">
                    <div class="lg:w-1/2">
                        <h3 class="text-2xl font-semibold text-morocco-blue">Rooted in Tradition</h3>
                        <p class="mt-2 text-gray-700">Our journey began in the historic souks of Marrakech, where artisans have crafted timeless treasures for centuries.</p>
                    </div>
                </div>
                
                <div class="flex flex-col lg:flex-row-reverse lg:items-center gap-8">
                    <img src="{{ asset('assets/images/Moroccan-weaving.jpg') }}" alt="Moroccan weaving" 
                         class="rounded-lg shadow-md w-full lg:w-1/2 h-64 object-cover border-2 border-morocco-green">
                    <div class="lg:w-1/2">
                        <h3 class="text-2xl font-semibold text-morocco-green">Empowering Artisans</h3>
                        <p class="mt-2 text-gray-700">We collaborate directly with skilled craftsmen and women, ensuring fair trade and community support.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Call to Action --}}
    <section class="bg-morocco-red py-16">
        <div class="max-w-5xl mx-auto px-6 text-center text-white">
            <h2 class="text-3xl sm:text-4xl font-bold">Join Us in Preserving Moroccan Heritage</h2>
            <p class="mt-4 text-lg">Explore our collections and bring a piece of Morocco into your home.</p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-white text-morocco-red font-semibold shadow hover:bg-gray-100 transition">
                    Shop Now
                </a>
                <a href="{{ route('contact') }}" class="px-6 py-3 rounded-xl border-2 border-white text-white font-semibold hover:bg-morocco-blue transition">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

@endsection
