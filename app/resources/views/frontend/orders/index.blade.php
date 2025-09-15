@extends('layouts.app')

@section('title', 'My Orders - AtlasShoop')
@section('meta_description', 'Track your orders on AtlasShoop. View details, payment status, and order history for your purchases of authentic Moroccan crafts.')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
    {{-- Page Header --}}
    <header class="mb-12 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-morocco-red">My Orders</h1>
        <p class="mt-4 text-gray-600 text-lg sm:text-xl max-w-2xl mx-auto">
            Track your orders and review details of your purchases at AtlasShoop.
        </p>
    </header>

    {{-- No Orders Message --}}
    @if($orders->isEmpty())
        <section class="bg-morocco-ivory p-6 rounded-lg shadow text-center">
            <p class="text-gray-500 text-lg">You have no orders yet.</p>
        </section>
    @else
        {{-- Orders Grid --}}
        <section class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($orders as $order)
                <article class="bg-white shadow-lg rounded-lg p-6 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                    <header>
                        <h2 class="text-xl font-semibold text-morocco-blue mb-2">Order #{{ $order->id }}</h2>
                        <dl class="text-gray-500 mb-3">
                            <div class="mb-1">
                                <dt class="font-medium inline">Date:</dt>
                                <dd class="inline">{{ $order->created_at->format('d M Y') }}</dd>
                            </div>
                            <div class="mb-1">
                                <dt class="font-medium inline">Total:</dt>
                                <dd class="inline">${{ number_format($order->total_amount, 2) }}</dd>
                            </div>
                            <div class="mb-1">
                                <dt class="font-medium inline">Status:</dt>
                                <dd class="inline">
                                    <span class="capitalize px-2 py-1 rounded text-white
                                        @switch($order->status)
                                            @case('paid') bg-morocco-green @break
                                            @case('pending') bg-yellow-500 @break
                                            @case('canceled') bg-red-500 @break
                                            @default bg-gray-400
                                        @endswitch
                                    ">
                                        {{ $order->status }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="font-medium inline">Payment:</dt>
                                <dd class="inline">{{ ucfirst($order->payment->method ?? 'N/A') }}</dd>
                            </div>
                        </dl>
                    </header>

                    <footer>
                        <a href="{{ route('frontend.orders.show', $order) }}" 
                           class="mt-4 block bg-morocco-red text-white px-4 py-2 rounded hover:bg-morocco-blue transition-colors duration-300 text-center font-medium">
                            View Details
                        </a>
                    </footer>
                </article>
            @endforeach
        </section>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    @endif

    {{-- About Section --}}
    <section class="mt-12 text-center text-gray-600 prose prose-lg max-w-3xl mx-auto">
        <h2>About AtlasShoop</h2>
        <p>
            We are dedicated to preserving Morocco’s timeless traditions through authentic handmade crafts — from pottery and weaving to intricate jewelry and woodwork.
        </p>
        <p>
            Every product tells a story of heritage, craftsmanship, and Moroccan identity.
        </p>
    </section>
</div>
@endsection
