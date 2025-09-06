@extends('layouts.app')

@section('title', "Order #{$order->id} Details | " . config('app.name'))
@section('meta_description', "View detailed information for order #{$order->id}, including client info, payment status, and ordered items.")

@section('content')
<div class="max-w-7xl mx-auto mt-8 p-6 bg-white rounded-lg shadow-lg">

    {{-- Page Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Order #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    {{-- Order Info --}}
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Client Info --}}
        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium mb-2">Client Information</h2>
            <p><span class="font-semibold">Name:</span> {{ $order->client->user?->name ?? 'Guest' }}</p>
            <p><span class="font-semibold">Email:</span> {{ $order->client->user?->email ?? 'N/A' }}</p>
            <p><span class="font-semibold">Phone:</span> {{ $order->client->phone ?? 'N/A' }}</p>
        </div>

        {{-- Payment Info --}}
        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium mb-2">Payment Information</h2>
            <p>
                <span class="font-semibold">Status:</span>
                <span class="px-2 py-1 rounded-full text-xs 
                    {{ $order->payment?->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($order->payment?->status ?? 'Pending') }}
                </span>
            </p>
            <p><span class="font-semibold">Total Amount:</span> {{ number_format($order->total_amount, 2) }} MAD</p>
            <p><span class="font-semibold">Payment Method:</span> {{ $order->payment?->method ?? 'N/A' }}</p>
            <p><span class="font-semibold">Paid At:</span> {{ $order->payment?->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
        </div>
    </div>

    {{-- Order Items --}}
    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm mb-6">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Product</th>
                    <th class="px-6 py-3 text-center">Quantity</th>
                    <th class="px-6 py-3 text-center">Unit Price</th>
                    <th class="px-6 py-3 text-center">Subtotal</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach ($order->orderItems as $item)
                    <tr class="border-t hover:bg-indigo-50 transition">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-center">{{ number_format($item->price, 2) }} MAD</td>
                        <td class="px-6 py-4 text-center">{{ number_format($item->price * $item->quantity, 2) }} MAD</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Order Summary --}}
    <div class="flex justify-end">
        <div class="bg-gray-50 p-4 rounded-lg shadow-sm w-full md:w-1/3">
            <p class="flex justify-between mb-2">
                <span>Items Total:</span>
                <span>{{ number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 2) }} MAD</span>
            </p>
            <p class="flex justify-between font-semibold text-lg">
                <span>Grand Total:</span>
                <span>{{ number_format($order->total_amount, 2) }} MAD</span>
            </p>
        </div>
    </div>

    {{-- Delete Order --}}
    <div class="mt-6 flex justify-end">
        <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}"
            onsubmit="return confirm('Are you sure you want to delete order #{{ $order->id }}?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-trash"></i> Delete Order
            </button>
        </form>
    </div>

</div>
@endsection
