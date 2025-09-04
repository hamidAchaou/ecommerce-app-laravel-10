@extends('layouts.app')

@section('title', 'Order Details #'.$order->id)

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold text-morocco-red">Order #{{ $order->id }}</h1>
        <a href="{{ route('frontend.orders.index') }}" 
           class="text-morocco-blue hover:underline font-medium">← Back to Orders</a>
    </div>

    <div class="grid gap-6 md:grid-cols-2 mb-8">
        <!-- Client Info -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-morocco-blue mb-4">Client Information</h2>
            <p class="mb-2"><span class="font-medium">Name:</span> {{ $order->client->name }}</p>
            <p class="mb-2"><span class="font-medium">Phone:</span> {{ $order->client->phone }}</p>
            <p class="mb-2"><span class="font-medium">Address:</span> {{ $order->client->address }}</p>
            <p class="mb-2"><span class="font-medium">City:</span> {{ $order->client->city->name ?? 'N/A' }}</p>
            <p class="mb-2"><span class="font-medium">Country:</span> {{ $order->client->country->name ?? 'N/A' }}</p>
        </div>

        <!-- Payment & Order Status -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-morocco-blue mb-4">Payment & Status</h2>
            <p class="mb-2"><span class="font-medium">Payment Method:</span> {{ ucfirst($order->payment->method ?? 'N/A') }}</p>
            <p class="mb-2"><span class="font-medium">Payment Status:</span> 
                <span class="px-2 py-1 rounded text-white
                    @if($order->status === 'paid') bg-morocco-green
                    @elseif($order->status === 'pending') bg-yellow-500
                    @elseif($order->status === 'canceled') bg-red-500
                    @else bg-gray-400
                    @endif
                ">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
            <p class="mb-2"><span class="font-medium">Total Amount:</span> ${{ number_format($order->total_amount,2) }}</p>
            <p class="mb-2"><span class="font-medium">Order Date:</span> {{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-morocco-blue mb-4">Order Items</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600">Product</th>
                        <th class="px-4 py-2 text-left text-gray-600">Quantity</th>
                        <th class="px-4 py-2 text-left text-gray-600">Price</th>
                        <th class="px-4 py-2 text-left text-gray-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($order->orderItems as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2 flex items-center gap-3">
                            <img src="{{ $item->product->mainImageUrl() }}" alt="{{ $item->product->title }}" class="w-12 h-12 object-cover rounded">
                            <span>{{ $item->product->title }}</span>
                        </td>
                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                        <td class="px-4 py-2">${{ number_format($item->price,2) }}</td>
                        <td class="px-4 py-2">${{ number_format($item->total,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-right">
            <p class="text-lg font-semibold">Grand Total: <span class="text-morocco-red">${{ number_format($order->total_amount,2) }}</span></p>
        </div>
    </div>
</div>
@endsection
