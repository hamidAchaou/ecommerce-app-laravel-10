@extends('layouts.app')

@section('title', 'Checkout | ' . config('app.name'))
@section('meta_description', 'Complete your purchase at ' . config('app.name') . '. Secure checkout for Moroccan handmade crafts.')

@section('content')
<section class="bg-morocco-ivory py-16">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Page Header --}}
        <header class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-morocco-red leading-tight">Checkout</h1>
            <p class="mt-2 text-lg text-gray-700">Provide your details and review your order.</p>
        </header>

        <div class="lg:flex lg:gap-12" x-data="cart()" x-init="init()">

            {{-- Checkout Form + Cart Items --}}
            <div class="lg:w-2/3 space-y-10">

                {{-- Customer Information --}}
                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Shipping Information</h2>

                    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- Full Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()?->name) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-morocco-red focus:border-morocco-red"
                                   required>
                            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-morocco-red focus:border-morocco-red"
                                   required>
                            @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea id="address" name="address" rows="3"
                                      class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-morocco-red focus:border-morocco-red"
                                      required>{{ old('address') }}</textarea>
                            @error('address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- City + Country --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <input type="text" id="city" name="city" value="{{ old('city') }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-morocco-red focus:border-morocco-red"
                                       required>
                                @error('city') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                                <input type="text" id="country" name="country" value="{{ old('country') }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-morocco-red focus:border-morocco-red"
                                       required>
                                @error('country') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Order Notes (optional)</label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-morocco-red focus:border-morocco-red">{{ old('notes') }}</textarea>
                        </div>
                    </form>
                </div>

                {{-- Cart Items --}}
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900">Your Items</h2>
                    <template x-if="cartItems.length > 0">
                        <div class="space-y-4">
                            <template x-for="item in cartItems" :key="item.id">
                                <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow hover:shadow-lg transition">
                                    <div class="flex items-center gap-4">
                                        <img :src="item.image" :alt="item.title" class="w-20 h-20 object-cover rounded-lg border-2 border-morocco-yellow">
                                        <div class="truncate">
                                            <h2 class="font-semibold text-gray-900 truncate" x-text="item.title"></h2>
                                            <p class="text-gray-600 mt-1" x-text="'$' + item.price.toFixed(2)"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-700" x-text="'x' + item.quantity"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="cartItems.length === 0">
                        <div class="text-center py-16">
                            <div class="mx-auto w-24 h-24 flex items-center justify-center rounded-full bg-gray-100 mb-6">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-700">Your cart is empty</h2>
                            <p class="mt-2 text-gray-500">Browse our products and find something you love.</p>
                            <a href="{{ route('products.index') }}"
                               class="mt-6 inline-block px-6 py-3 bg-morocco-red text-white font-semibold rounded-xl shadow hover:bg-red-700 transition">
                                Continue Shopping
                            </a>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Order Summary --}}
            <aside class="lg:w-1/3 mt-6 lg:mt-0 bg-white rounded-xl shadow p-6 h-fit">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Order Summary</h2>

                <div class="space-y-3">
                    <div class="flex justify-between text-gray-700">
                        <span>Subtotal</span>
                        <span x-text="'$' + getCartTotal().toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>Shipping</span>
                        <span class="text-green-600">Free</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-bold text-gray-900">
                        <span>Total</span>
                        <span x-text="'$' + getCartTotal().toFixed(2)"></span>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <button type="submit" form="checkout-form"
                            class="block text-center w-full px-4 py-3 bg-morocco-red text-white font-semibold rounded-xl shadow hover:bg-morocco-blue transition">
                        Confirm & Place Order
                    </button>
                    <button @click="clearCart()"
                            class="w-full py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition"
                            x-show="cartItems.length > 0">
                        Clear Cart
                    </button>
                    <a href="{{ route('products.index') }}" 
                       class="block text-center w-full px-4 py-3 border-2 border-morocco-red text-morocco-red font-semibold rounded-xl hover:bg-morocco-red hover:text-white transition">
                        Continue Shopping
                    </a>
                </div>
            </aside>

        </div>
    </div>
</section>
@endsection
