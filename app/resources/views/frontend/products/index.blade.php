@extends('layouts.app')

@section('title', 'Our Products')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <x-frontend.section-header 
            title="Our Products" 
            subtitle="Explore our latest products with unbeatable quality" 
        />

        @if($products->isEmpty())
            <p class="text-center text-gray-500 mt-10">No products available at the moment.</p>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-frontend.product.card :product="$product" />
                @endforeach
            </div>

            <div class="mt-10 flex justify-center">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
