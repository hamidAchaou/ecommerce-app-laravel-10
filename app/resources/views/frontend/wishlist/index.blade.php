@extends('layouts.app')

@section('title', 'My Wishlist')
@section('meta_description', 'View all your favorite products. Save items to your wishlist for later purchase.')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-7xl mx-auto">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-4xl font-extrabold text-morocco-blue">My Wishlist</h1>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-morocco-green/10 text-morocco-green border border-morocco-green px-6 py-4 mb-8 rounded-lg text-lg font-medium" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Wishlist Content --}}
        @if($wishlist->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center bg-gray-50 rounded-lg shadow-inner">
                <i class="fas fa-heart-broken text-6xl text-morocco-red mb-4"></i>
                <p class="text-xl text-gray-700 font-semibold mb-4">Your wishlist is currently empty.</p>
                <p class="text-md text-gray-500 mb-6">Add products you love to save them for later.</p>
                <a href="{{ route('products.index') }}" class="bg-morocco-blue text-white px-6 py-3 rounded-full font-semibold hover:bg-morocco-blue/90 transition-colors duration-200">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($wishlist as $item)
                    <div class="relative bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden group hover:shadow-xl transition-shadow duration-300">
                        {{-- Remove button for desktop --}}
                        <form action="{{ route('wishlist.destroy', $item->product->id) }}" method="POST" class="absolute top-3 right-3 z-10 hidden sm:block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-full bg-white text-morocco-red shadow-md hover:bg-gray-100 transition-colors" aria-label="Remove {{ $item->product->name }} from wishlist">
                                <i class="fas fa-trash-alt text-lg"></i>
                            </button>
                        </form>
                        
                        <a href="{{ route('products.show', $item->product->id) }}" class="block">
                            <img src="{{ $item->product->image_url ?? 'https://via.placeholder.com/600x400?text=Product+Image' }}" alt="{{ $item->product->name }}" class="w-full h-56 object-cover rounded-t-lg">
                            <div class="p-4">
                                <h2 class="text-lg font-semibold text-gray-800 truncate mb-1">{{ $item->product->name }}</h2>
                                <p class="text-2xl font-bold text-morocco-blue">${{ number_format($item->product->price, 2) }}</p>
                            </div>
                        </a>

                        {{-- Remove button for mobile --}}
                        <form action="{{ route('wishlist.destroy', $item->product->id) }}" method="POST" class="px-4 pb-4 block sm:hidden">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-morocco-red text-white py-2 rounded-lg font-medium hover:bg-morocco-red/90 transition-colors" aria-label="Remove {{ $item->product->name }} from wishlist">
                                <i class="fas fa-trash-alt mr-2"></i>Remove
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection