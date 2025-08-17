@extends('layouts.frontend.frontend-wrapper')

@section('title', $product->name)

@section('content')
    <div class="container section">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            
            <!-- Product Images -->
            <div>
                <div class="aspect-product bg-gray-100 overflow-hidden rounded-lg shadow-sm">
                    <img src="{{ $product->mainImageUrl() }}"
                         alt="{{ $product->name }}"
                         class="object-cover w-full h-full">
                </div>
                @if($product->images->count() > 1)
                    <div class="flex gap-3 mt-4">
                        @foreach($product->images as $image)
                            <div class="w-20 h-20 bg-gray-100 rounded overflow-hidden cursor-pointer">
                                <img src="{{ $image->url }}"
                                     alt="{{ $product->name }}"
                                     class="object-cover w-full h-full hover:scale-110 transition-fast">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <h1 class="text-3xl font-bold text-secondary">{{ $product->name }}</h1>
                <p class="mt-4 text-primary text-2xl font-semibold">${{ number_format($product->price, 2) }}</p>
                
                <p class="mt-6 text-gray-600 leading-relaxed">
                    {{ $product->description }}
                </p>

                <!-- Add to Cart -->
                <form action="{{ route('cart.store', $product) }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                    </button>
                </form>

                <!-- Categories -->
                @if($product->categories->isNotEmpty())
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-muted">Categories:</h4>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($product->categories as $category)
                                <span class="px-3 py-1 text-sm bg-muted rounded-full text-gray-700">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
