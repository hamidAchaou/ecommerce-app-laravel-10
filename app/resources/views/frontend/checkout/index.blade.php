@extends('layouts.app')

@section('title', 'Checkout | ' . config('app.name'))
@section('meta_description', 'Complete your purchase at ' . config('app.name') . '. Secure checkout for Moroccan handmade crafts.')

@section('content')
<section class="bg-morocco-ivory py-16">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Page Header --}}
        <header class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-morocco-red leading-tight">Checkout</h1>
            <p class="mt-2 text-lg text-gray-700">Review your order and proceed securely.</p>
        </header>

        <div class="lg:flex lg:gap-12" x-data="cart()" x-init="init()">

            {{-- Cart Items --}}
            <div class="lg:w-2/3 space-y-6">
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
                                    <input type="number" min="1" class="w-16 text-center rounded border-gray-300" 
                                           :value="item.quantity" 
                                           @change="updateQuantity(item.id, $event.target.value)">
                                    <button @click="removeFromCart(item.id)" 
                                            class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 transition">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Empty Cart --}}
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

            {{-- Order Summary --}}
            <aside class="lg:w-1/3 mt-6 lg:mt-0 bg-white rounded-xl shadow p-6">
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
                    <a href="{{ route('payment.index') }}" 
                       class="block text-center w-full px-4 py-3 bg-morocco-red text-white font-semibold rounded-xl shadow hover:bg-morocco-blue transition">
                        Proceed to Payment
                    </a>
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