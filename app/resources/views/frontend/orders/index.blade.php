@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
    <h1 class="text-4xl font-extrabold mb-6 text-morocco-red">My Orders</h1>
    <p class="mb-8 text-gray-600 text-lg">Track your orders and review details of your purchases at AtlasShoop.</p>

    @if($orders->isEmpty())
        <div class="bg-morocco-ivory p-6 rounded-lg shadow text-center">
            <p class="text-gray-500 text-lg">You have no orders yet.</p>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($orders as $order)
            <div class="bg-white shadow-lg rounded-lg p-6 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <h2 class="text-xl font-semibold text-morocco-blue mb-2">Order #{{ $order->id }}</h2>
                    <p class="text-gray-500 mb-1"><span class="font-medium">Date:</span> {{ $order->created_at->format('d M Y') }}</p>
                    <p class="text-gray-500 mb-1"><span class="font-medium">Total:</span> ${{ number_format($order->total_amount, 2) }}</p>
                    <p class="mb-1">
                        <span class="font-medium">Status:</span>
                        <span class="capitalize px-2 py-1 rounded text-white
                            @if($order->status === 'paid') bg-morocco-green
                            @elseif($order->status === 'pending') bg-yellow-500
                            @elseif($order->status === 'canceled') bg-red-500
                            @else bg-gray-400
                            @endif
                        ">
                            {{ $order->status }}
                        </span>
                    </p>
                    <p class="text-gray-500 mb-3"><span class="font-medium">Payment:</span> {{ ucfirst($order->payment->method ?? 'N/A') }}</p>
                </div>

                <a href="{{ route('frontend.orders.show', $order) }}" 
                   class="mt-4 inline-block bg-morocco-red text-white px-4 py-2 rounded hover:bg-morocco-blue transition-colors duration-300 text-center font-medium">
                    View Details
                </a>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    @endif

    <div class="mt-12 text-center text-gray-600 prose prose-lg max-w-3xl mx-auto">
        <h2>About AtlasShoop</h2>
        <p>
            We are dedicated to preserving Morocco’s timeless traditions through authentic handmade crafts — from pottery and weaving to intricate jewelry and woodwork.
        </p>
        <p>
            Every product tells a story of heritage, craftsmanship, and Moroccan identity.
        </p>
    </div>
</div>
@endsection
