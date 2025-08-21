@extends('layouts.app')

@section('title', 'Payment | ' . config('app.name'))
@section('meta_description', 'Secure payment for your order at ' . config('app.name') . '.')

@section('content')
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Page Header --}}
        <header class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-morocco-red leading-tight">Payment</h1>
            <p class="mt-2 text-lg text-gray-700">Complete your purchase securely.</p>
        </header>

        <div class="lg:flex lg:gap-12">

            {{-- Payment Options --}}
            <div class="lg:w-2/3 space-y-6">
                <div class="bg-white rounded-xl shadow p-6 space-y-4">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Select Payment Method</h2>

                    <div class="space-y-3">
                        {{-- Stripe --}}
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-morocco-red transition">
                            <input type="radio" name="payment_method" value="stripe" class="form-radio">
                            <span class="font-semibold text-gray-900">Credit / Debit Card (Stripe)</span>
                        </label>

                        {{-- PayPal --}}
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-morocco-red transition">
                            <input type="radio" name="payment_method" value="paypal" class="form-radio">
                            <span class="font-semibold text-gray-900">PayPal</span>
                        </label>

                        {{-- Cash on Delivery --}}
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-morocco-red transition">
                            <input type="radio" name="payment_method" value="cod" class="form-radio">
                            <span class="font-semibold text-gray-900">Cash on Delivery</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <aside class="lg:w-1/3 mt-6 lg:mt-0 bg-white rounded-xl shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Order Summary</h2>

                @foreach($cartItems as $item)
                    <div class="flex justify-between mb-2">
                        <span>{{ $item['title'] }} x {{ $item['quantity'] }}</span>
                        <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                @endforeach

                <div class="border-t mt-3 pt-3 flex justify-between font-bold text-gray-900">
                    <span>Total</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>

                {{-- Proceed to Payment Button --}}
                <form action="{{ route('checkout.process') }}" method="POST" class="mt-6">
                    @csrf
                    <input type="hidden" name="payment_method" value="" x-ref="payment_method_input">
                    <button type="submit"
                        class="w-full py-3 bg-morocco-red text-white font-semibold rounded-xl shadow hover:bg-morocco-blue transition"
                        x-on:click.prevent="
                            const selected = document.querySelector('input[name=payment_method]:checked');
                            if(selected) { $refs.payment_method_input.value = selected.value; $el.submit(); } 
                            else { alert('Please select a payment method'); }
                        ">
                        Pay Now
                    </button>
                </form>

                <a href="{{ route('checkout.index') }}"
                   class="block mt-4 text-center text-morocco-red font-semibold hover:underline">
                   Back to Checkout
                </a>
            </aside>

        </div>
    </div>
</section>
@endsection
