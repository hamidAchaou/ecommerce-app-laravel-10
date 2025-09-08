@extends('layouts.app')

@section('title', 'Checkout | ' . config('app.name'))
@section('meta_description', 'Complete your purchase at ' . config('app.name') . '. Secure checkout for Moroccan
    handmade crafts.')

@section('content')
    <section class="bg-morocco-ivory py-16">
        <div class="max-w-7xl mx-auto px-6">
            {{-- Page Header --}}
            <header class="text-center mb-12">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-morocco-red leading-tight">Checkout</h1>
                <p class="mt-2 text-lg text-gray-700">Provide your details and review your order.</p>
            </header>

            <div x-data="checkoutPage()" class="lg:flex lg:gap-12">
                {{-- Checkout Form --}}
                <div class="lg:w-2/3 space-y-10">
                    <div class="bg-white p-6 rounded-xl shadow">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Shipping Information</h2>

                        @if ($client && $client->phone && $client->address && $client->country_id && $client->city_id)
                            {{-- Show saved client info --}}
                            <div class="space-y-2 text-gray-700">
                                <p><strong>Name:</strong> {{ $client->user->name }}</p>
                                <p><strong>Phone:</strong> {{ $client->phone }}</p>
                                <p><strong>Address:</strong> {{ $client->full_address }}</p>
                            </div>
                            <p class="mt-3 text-sm text-gray-500">
                                Not correct?
                                <a href="{{ route('profile.edit') }}"
                                    class="text-morocco-red font-medium hover:underline">Update your profile</a>
                            </p>
                        @else
                            {{-- Show shipping form --}}
                            <form id="checkout-form" class="space-y-5">
                                @csrf
                                <x-frontend.form.input id="name" name="name" label="Full Name"
                                    value="{{ old('name', auth()->user()?->name) }}" required />
                                <x-frontend.form.input id="phone" name="phone" label="Phone Number"
                                    value="{{ old('phone') }}" required />
                                <x-frontend.form.textarea id="address" name="address" label="Address" rows="3"
                                    required>{{ old('address') }}</x-frontend.form.textarea>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-frontend.form.select id="country_id" name="country_id" label="Country"
                                        x-model="selectedCountry" required>
                                        <option value="">-- Select Country --</option>
                                        <template x-for="country in countries" :key="country.id">
                                            <option :value="country.id" x-text="country.name"></option>
                                        </template>
                                    </x-frontend.form.select>

                                    <x-frontend.form.select id="city_id" name="city_id" label="City"
                                        x-model="selectedCity" :disabled="!selectedCountry" required>
                                        <option value="">-- Select City --</option>
                                        <template x-for="city in filteredCities" :key="city.id">
                                            <option :value="city.id" x-text="city.name"></option>
                                        </template>
                                    </x-frontend.form.select>
                                </div>

                                <x-frontend.form.textarea id="notes" name="notes" label="Order Notes (optional)"
                                    rows="2">{{ old('notes') }}</x-frontend.form.textarea>
                            </form>
                        @endif
                    </div>

                    {{-- Cart Items --}}
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-gray-900">Your Items</h2>
                        <template x-if="cartItems.length > 0">
                            <div class="space-y-4">
                                <template x-for="item in cartItems" :key="item.id">
                                    <div
                                        class="flex items-center justify-between p-4 bg-white rounded-xl shadow hover:shadow-lg transition">
                                        <div class="flex items-center gap-4">
                                            <img :src="item.image" :alt="item.title"
                                                class="w-20 h-20 object-cover rounded-lg border-2 border-morocco-yellow">
                                            <div class="truncate">
                                                <h2 class="font-semibold text-gray-900 truncate" x-text="item.title"></h2>
                                                <p class="text-gray-600 mt-1" x-text="'$' + item.price.toFixed(2)"></p>
                                            </div>
                                        </div>
                                        <span class="text-gray-700" x-text="'x' + item.quantity"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="cartItems.length === 0">
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto w-24 h-24 flex items-center justify-center rounded-full bg-gray-100 mb-6">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4z" />
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-semibold text-gray-700">Your cart is empty</h2>
                                <p class="mt-2 text-gray-500">Browse our products and find something you love.</p>
                                <x-frontend.button.button-primary :href="route('products.index')">Continue
                                    Shopping</x-frontend.button.button-primary>
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
                        {{-- Stripe Payment --}}
                        <x-frontend.button.button-primary type="button" @click="payWithStripe"
                            class="w-full flex items-center justify-center gap-2 relative disabled:opacity-70 disabled:cursor-not-allowed transition-all duration-200"
                            x-bind:disabled="isLoadingPayment">
                            <template x-if="isLoadingPayment">
                                <svg class="animate-spin h-5 w-5 text-white absolute left-4"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                    </path>
                                </svg>
                            </template>
                            <span x-text="isLoadingPayment ? 'Processing...' : 'Pay with Stripe'"></span>
                        </x-frontend.button.button-primary>

                        {{-- Clear Cart --}}
                        <button @click="clearCart()"
                            class="w-full py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition"
                            x-show="cartItems.length > 0">
                            Clear Cart
                        </button>

                        {{-- Continue Shopping --}}
                        <x-frontend.button.button-primary :href="route('products.index')"
                            class="w-full bg-white text-red-600 border-red-600 hover:bg-red-600 hover:text-white">
                            Continue Shopping
                        </x-frontend.button.button-primary>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function checkoutPage() {
            return {
                cartItems: @json($cartItems),
                countries: @json($countries),
                selectedCountry: "{{ old('country_id') }}",
                selectedCity: "{{ old('city_id') }}",
                isLoadingPayment: false,

                get filteredCities() {
                    const country = this.countries.find(c => c.id == this.selectedCountry);
                    return country ? country.cities : [];
                },

                getCartTotal() {
                    return this.cartItems.reduce((acc, item) => acc + item.price * item.quantity, 0);
                },

                async clearCart() {
                    try {
                        const res = await fetch('/cart/clear/all', {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        });
                        if (res.ok) this.cartItems = [];
                    } catch (err) {
                        console.error(err);
                    }
                },

                async payWithStripe() {
                    if (this.isLoadingPayment) return;
                    if (this.cartItems.length === 0) {
                        alert('Cart is empty');
                        return;
                    }

                    const form = document.getElementById('checkout-form');
                    if (form && !form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    let payload = {};
                    if (form) {
                        payload = {
                            name: form.name.value,
                            phone: form.phone.value,
                            address: form.address.value,
                            country_id: form.country_id.value,
                            city_id: form.city_id.value,
                            notes: form.notes.value || ''
                        };
                    } else if (@json($client)) {
                        payload = {
                            name: @json($client->user->name ?? ''),
                            phone: @json($client->phone ?? ''),
                            address: @json($client->address ?? ''),
                            country_id: @json($client->country_id ?? ''),
                            city_id: @json($client->city_id ?? ''),
                            notes: ''
                        };
                    } else {
                        alert('Client info missing');
                        return;
                    }

                    this.isLoadingPayment = true;

                    try {
                        const res = await fetch("{{ route('checkout.stripe') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(payload)
                        });
                        if (!res.ok) throw new Error(await res.text());

                        const data = await res.json();
                        if (data.error) {
                            alert(data.error);
                            this.isLoadingPayment = false;
                            return;
                        }

                        const stripe = Stripe("{{ config('services.stripe.key') }}");
                        await stripe.redirectToCheckout({
                            sessionId: data.id
                        });
                    } catch (err) {
                        console.error(err);
                        alert('Payment failed. Try again.');
                    } finally {
                        this.isLoadingPayment = false;
                    }
                }
            }
        }
    </script>
@endsection
