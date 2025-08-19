@extends('layouts.app')

@section('title', $product->title . ' – AtlasShoop')
@section('meta_description', Str::limit(strip_tags($product->description), 160))

@section('content')
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Product Images --}}
            <div class="lg:col-span-5">
                {{-- <x-frontend.product.product-images :images="$product->images" :title="$product->title" /> --}}
                <x-frontend.product.product-images :images="$product->images" :title="$product->title" />

            </div>

            {{-- Product Details --}}
            <div class="lg:col-span-7 flex flex-col justify-between space-y-6">
                <article>
                    <h1 class="text-3xl font-bold text-morocco-red">{{ $product->title }}</h1>

                    {{-- Ratings --}}
                    @if (isset($product->rating))
                        <x-frontend.product.product-rating :rating="$product->rating" :reviews-count="$product->reviews_count ?? 0" />
                    @endif

                    {{-- Price & Discount --}}
                    <x-frontend.product.product-price :price="$product->price" :discount-price="$product->discount_price" />

                    {{-- Stock Status --}}
                    <x-frontend.product.product-stock :stock="$product->stock" />

                    {{-- Delivery Estimate --}}
                    <p class="mt-1 text-gray-600">
                        <i class="fas fa-truck"></i>
                        Free delivery by <strong>{{ now()->addDays(3)->format('l, M d') }}</strong>
                    </p>

                    {{-- Add to Cart --}}
                    <div class="mt-4 space-y-3">
                        <x-frontend.product.add-to-cart :product="$product" />
                    </div>

                    {{-- Description --}}
                    <x-frontend.product.product-description :description="$product->description" />
                </article>
            </div>
        </div>

        {{-- Related Products --}}
        <x-frontend.product.related-products :products="$relatedProducts" />
    </div>
@endsection
