@extends('layouts.app')

@section('title', 'Your Shopping Cart | ' . config('app.name'))
@section('meta_description', 'View and manage your shopping cart at ' . config('app.name') . '. Complete your order for
    authentic Moroccan handmade crafts.')

@section('content')

    {{-- Page Header --}}
    <section class="bg-gradient-to-r from-red-50 via-white to-red-50 py-12 border-b">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                Your Shopping Cart
            </h1>
            <p class="mt-3 text-lg text-gray-600">
                Review your items and proceed to checkout securely.
            </p>
        </div>
    </section>

    {{-- Cart Content --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            @if ($cartItems->isEmpty())
                {{-- Empty Cart State --}}
                <div class="text-center py-16">
                    <img src="{{ asset('assets/images/empty-cart.svg') }}" alt="Empty cart illustration"
                        class="mx-auto w-40 h-40 mb-6" loading="lazy">
                    <h2 class="text-2xl font-semibold text-gray-700">Your cart is empty</h2>
                    <p class="mt-2 text-gray-500">Browse our products and find something you love.</p>
                    <a href="{{ route('products.index') }}"
                        class="mt-6 inline-block px-6 py-3 bg-morocco-red text-white font-semibold rounded-xl shadow hover:bg-red-700 transition">
                        Continue Shopping
                    </a>
                </div>
            @else
                {{-- Cart Items Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-xl shadow-sm bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Price</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Quantity</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Total</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($cartItems as $item)
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Product --}}
                                <td class="px-6 py-4 flex items-center gap-4">
                                    <img src="{{ asset($item['image'] ?? 'images/no-image.png') }}"
                                        alt="{{ $item['title'] }}"
                                        class="w-16 h-16 object-cover rounded-lg border" loading="lazy">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $item['title'] }}</h3>
                                    </div>
                                </td>
                                {{-- Price --}}
                                <td class="px-6 py-4 text-center text-gray-700 font-medium">
                                    ${{ number_format($item['price'], 2) }}
                                </td>
                                {{-- Quantity --}}
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                            min="1"
                                            class="w-16 text-center border rounded-md focus:ring-red-500 focus:border-red-500">
                                        <button type="submit" class="text-sm text-morocco-blue hover:underline">Update</button>
                                    </form>
                                </td>
                                {{-- Total --}}
                                <td class="px-6 py-4 text-center text-gray-900 font-bold">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </td>
                                {{-- Remove --}}
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('cart.destroy', $item['id']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>

                {{-- Cart Summary --}}
                <div class="mt-8 lg:flex lg:justify-between lg:items-start">
                    <div class="lg:w-2/3">
                        <a href="{{ route('products.index') }}"
                            class="inline-block px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition">
                            Continue Shopping
                        </a>
                    </div>

                    <div class="lg:w-1/3 bg-gray-50 p-6 rounded-xl shadow-md mt-6 lg:mt-0">
                        <h3 class="text-xl font-semibold text-gray-900">Order Summary</h3>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Shipping</span>
                                <span class="text-green-600">Free</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between font-bold text-gray-900">
                                <span>Total</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('checkout.index') }}"
                            class="mt-6 block w-full text-center px-6 py-3 bg-morocco-red text-white font-semibold rounded-xl shadow hover:bg-red-700 transition">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection
