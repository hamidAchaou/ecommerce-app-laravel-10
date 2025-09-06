@extends('layouts.app')

@section('title', 'Our Products')
@section('meta_description', 'Browse Moroccan crafts, pottery, weaving, woodwork, jewelry, and leatherwork. Filter by price and category.')

@section('content')
<div class="container mx-auto px-4 py-12">

    {{--  Search + Sort in One Row --}}
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow p-4 hover:shadow-md transition duration-300 flex flex-col sm:flex-row items-center gap-4 sm:justify-between">
            
            {{-- Search Bar (50% width on larger screens) --}}
            <div class="w-full sm:w-1/2">
                <x-frontend.filters.search />
            </div>
    
            {{-- Sort Dropdown (Fixed width) --}}
            <div class="w-full sm:w-48 flex-shrink-0">
                <x-frontend.filters.sort />
            </div>
        </div>
    </div>
    

    <div class="grid lg:grid-cols-4 gap-8">
        {{--  Sidebar Filters --}}
        <aside class="space-y-6 lg:col-span-1">
            <x-frontend.filters.price />
            <x-frontend.filters.categories :categories="$categories" />
        </aside>

        {{-- Products Section --}}
        <section class="lg:col-span-3">
            
            {{-- Active Filters --}}
            <div class="mb-6">
                @include('frontend.products.partials.active-filters', ['categories' => $categories])
            </div>

            {{-- Products Grid --}}
            @if ($products->isEmpty())
                <p class="text-center text-gray-500 mt-10">
                    No products available at the moment.
                </p>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <x-frontend.product.card :product="$product" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10 flex justify-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
