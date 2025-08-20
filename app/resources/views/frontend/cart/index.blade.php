@extends('layouts.app')

@section('title', 'Your Shopping Cart | ' . config('app.name'))
@section('meta_description', 'View and manage your shopping cart at ' . config('app.name') . '. Complete your order for authentic Moroccan handmade crafts.')

@section('content')

{{-- Cart Page Container --}}
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4" x-data="cart()" x-init="init()">

        {{-- Page Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Your Shopping Cart</h1>
            <p class="mt-2 text-lg text-gray-600">Review your items and proceed to checkout securely.</p>
        </div>

        {{-- Cart Content --}}
        <template x-if="cartItems.length > 0">
            <div class="lg:flex lg:gap-8">

                {{-- Cart Items Table --}}
                <div class="lg:w-2/3 overflow-x-auto rounded-xl shadow bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-sm font-semibold text-gray-700">Product</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Price</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Quantity</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Total</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="item in cartItems" :key="item.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Product --}}
                                    <td class="px-6 py-4 flex items-center gap-4">
                                        <img :src="item.image" :alt="item.title" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                        <div class="truncate">
                                            <h3 class="font-semibold text-gray-800 truncate" x-text="item.title"></h3>
                                        </div>
                                    </td>

                                    {{-- Price --}}
                                    <td class="px-6 py-4 text-center text-gray-700 font-medium" x-text="`$${item.price.toFixed(2)}`"></td>

                                    {{-- Quantity --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center border rounded-lg overflow-hidden">
                                            <button type="button" @click="updateQuantity(item.id, item.quantity - 1)" 
                                                    class="px-3 py-1 bg-gray-100 hover:bg-gray-200 transition"
                                                    :disabled="item.quantity <= 1">
                                                <i class="fas fa-minus text-gray-700"></i>
                                            </button>
                                            <span class="w-12 text-center border-l border-r border-gray-200" x-text="item.quantity"></span>
                                            <button type="button" @click="updateQuantity(item.id, item.quantity + 1)" 
                                                    class="px-3 py-1 bg-gray-100 hover:bg-gray-200 transition">
                                                <i class="fas fa-plus text-gray-700"></i>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-4 text-center font-bold text-gray-900" x-text="`$${(item.price * item.quantity).toFixed(2)}`"></td>

                                    {{-- Remove --}}
                                    <td class="px-6 py-4 text-center">
                                        <button @click="removeFromCart(item.id)" class="text-red-600 hover:text-red-800 transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Cart Summary --}}
                <div class="lg:w-1/3 mt-6 lg:mt-0 bg-gray-50 p-6 rounded-xl shadow-md">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span x-text="`$${getCartTotal().toFixed(2)}`"></span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Shipping</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between font-bold text-gray-900">
                            <span>Total</span>
                            <span x-text="`$${getCartTotal().toFixed(2)}`"></span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-2">
                        <a href="{{ route('products.index') }}" 
                           class="block text-center px-3 py-2 border border-morocco-red rounded-lg text-morocco-red font-medium hover:bg-morocco-red hover:text-white transition">
                            Continue Shopping
                        </a>
                        <a href="{{ route('checkout.index') }}" 
                           class="block text-center px-3 py-2 bg-morocco-red text-white rounded-lg font-medium hover:bg-morocco-blue transition">
                            Checkout
                        </a>
                    </div>

                    <button @click="clearCart()" 
                            class="mt-4 w-full py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition"
                            x-show="cartItems.length > 0">
                        Clear Cart
                    </button>
                </div>

            </div>
        </template>

        {{-- Empty Cart --}}
        <template x-if="cartItems.length === 0">
            <div class="text-center py-16">
                {{-- Icon instead of image --}}
                <div class="mx-auto w-20 h-20 mb-6 flex items-center justify-center rounded-full bg-gray-100">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                </div>
        
                {{-- Title --}}
                <h2 class="text-2xl font-semibold text-gray-700">Your cart is empty</h2>
        
                {{-- Description --}}
                <p class="mt-2 text-gray-500">Browse our products and find something you love.</p>
        
                {{-- Call to Action --}}
                <a href="{{ route('products.index') }}"
                   class="mt-6 inline-block px-6 py-3 bg-morocco-red text-white font-semibold rounded-xl shadow hover:bg-red-700 transition">
                    Continue Shopping
                </a>
            </div>
        </template>
        

    </div>
</section>

@endsection
