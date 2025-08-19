@extends('layouts.app')

@section('title', 'Our Products')
@section('meta_description', 'Browse Moroccan crafts, pottery, weaving, woodwork, jewelry, and leatherwork. Filter by price and category.')

@section('content')
<div class="container mx-auto px-4 py-12 grid lg:grid-cols-4 gap-8">

    {{-- Sidebar Filters --}}
    <aside class="space-y-6 lg:col-span-1">
        <x-frontend.filters.search />
        <x-frontend.filters.categories :categories="$categories" />
        <x-frontend.filters.price />
    </aside>

    {{-- Products Grid --}}
    <div class="lg:col-span-3">

        {{-- Active Filters --}}
        @include('frontend.products.partials.active-filters', ['categories' => $categories])

        @if ($products->isEmpty())
            <p class="text-center text-gray-500 mt-10">No products available at the moment.</p>
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
    </div>
</div>
@endsection
