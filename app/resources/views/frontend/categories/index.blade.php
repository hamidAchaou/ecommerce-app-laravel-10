@extends('layouts.app')

@section('title', 'Categories | ' . config('app.name'))
@section('meta_description', 'Browse Moroccan craft categories including textiles, leatherwork, pottery, jewelry, and woodwork — preserving Morocco’s cultural heritage.')

@section('content')

    {{-- Hero --}}
    <section class="relative bg-gradient-to-r from-red-50 via-white to-red-50 py-12">
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900">
                Explore <span class="text-red-600">Categories</span>
            </h1>
            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                Discover Morocco’s timeless traditions through our artisan categories — from weaving and pottery to jewelry and woodwork.
            </p>
        </div>
        <div class="absolute inset-0 opacity-10 bg-[url('/assets/images/pattern-morocco.svg')] bg-repeat"></div>
    </section>

    {{-- Categories Section --}}
    <section class="bg-morocco-ivory py-16">
        <div class="max-w-7xl mx-auto px-6">

            {{-- Header + Search --}}
            {{-- <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-12 gap-4">
                <h2 class="text-3xl sm:text-4xl font-bold text-morocco-red">All Categories</h2>

                <div class="relative w-full sm:w-80">
                    <!-- Icon -->
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                        </svg>
                    </span>

                    <!-- Input -->
                    <input type="text" id="categorySearch" placeholder="Search categories..."
                           class="w-full pl-10 pr-10 py-3 rounded-xl border border-gray-300 shadow-sm
                                  focus:ring-2 focus:ring-morocco-blue focus:border-morocco-blue
                                  outline-none transition-all duration-200">

                    <!-- Clear Button -->
                    <button type="button" id="clearSearch"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 hidden">
                        ✕
                    </button>
                </div>
            </div> --}}

            {{-- Categories --}}
            <div class="space-y-16" id="categoriesWrapper">
                @foreach ($categories as $index => $category)
                    <div class="category-item flex flex-col {{ $index % 2 == 0 ? 'lg:flex-row' : 'lg:flex-row-reverse' }} lg:items-center gap-8"
                         data-name="{{ strtolower($category->name) }} {{ strtolower($category->description) }}">
                        
                        {{-- Category Image --}}
                        <img src="{{ asset($category->image) }}" 
                             alt="{{ $category->name }}" 
                             loading="lazy"
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

    {{-- Search Script --}}
    @push('scripts')
    <script>
        const searchInput = document.getElementById("categorySearch");
        const clearBtn = document.getElementById("clearSearch");
        const categories = document.querySelectorAll(".category-item");

        searchInput.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            clearBtn.classList.toggle("hidden", query.length === 0);

            categories.forEach(item => {
                const text = item.getAttribute("data-name");
                item.style.display = text.includes(query) ? "flex" : "none";
            });
        });

        clearBtn.addEventListener("click", function () {
            searchInput.value = "";
            clearBtn.classList.add("hidden");
            categories.forEach(item => item.style.display = "flex");
        });
    </script>
    @endpush

@endsection
