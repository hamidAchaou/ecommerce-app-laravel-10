@extends('layouts.app')

@section('title', 'Order Success | ' . config('app.name'))

@section('content')
<section class="bg-morocco-ivory py-16">
    <div class="max-w-4xl mx-auto px-6 text-center">
        {{-- Success Message --}}
        <h1 class="text-4xl font-bold text-green-600">Payment Successful 🎉</h1>
        <p class="mt-4 text-gray-700">Thank you for your purchase. Your order is being processed.</p>

        {{-- Order Details --}}
        @if($order)
            <div class="mt-10 bg-white rounded-xl shadow p-6 text-left">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Order Details</h2>
                <div class="space-y-3 text-gray-700">
                    <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                    <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                    <p>
                        <strong>Status:</strong>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold text-white bg-{{ $order->status_color }}-500">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    <p><strong>Total Items:</strong> {{ $order->total_items }}</p>
                </div>

                {{-- Order Items --}}
                <div class="mt-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Items</h3>
                    <div class="divide-y divide-gray-200">
                        @foreach($order->orderItems as $item)
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ asset('storage/' . ($item->product->images->first()->image_path ?? 'placeholder.jpg')) }}"
                                         alt="{{ $item->product->title ?? 'Product' }}"
                                         class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $item->product->title ?? 'Product' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Qty: {{ $item->quantity }}
                                        </p>
                                    </div>
                                </div>
                                <div class="font-semibold text-gray-900">
                                    ${{ number_format($item->price * $item->quantity, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="mt-10 bg-white rounded-xl shadow p-6">
                <p class="text-gray-600">We couldn’t find details for this order.</p>
            </div>
        @endif

        {{-- Continue Shopping --}}
        <a href="{{ route('products.index') }}"
           class="mt-8 inline-block px-6 py-3 bg-morocco-red text-white rounded-xl shadow hover:bg-red-700 transition">
            Continue Shopping
        </a>
    </div>
</section>
@endsection
