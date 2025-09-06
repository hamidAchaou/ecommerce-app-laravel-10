@extends('layouts.app')

@section('title', 'Orders Dashboard | ' . config('app.name'))
@section('meta_description', 'Manage all customer orders efficiently. View details, payments, statuses, and filter orders.')

@section('content')
<div class="max-w-7xl mx-auto mt-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-3 items-center w-full md:w-auto">

            {{-- Search Input --}}
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search client, email, or order ID..."
                    class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-morocco-blue focus:border-morocco-blue shadow-sm transition duration-150">

                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-search"></i>
                </span>
            </div>

            {{-- Payment Status --}}
            <select name="payment_status"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-morocco-blue focus:border-morocco-blue transition">
                <option value="">All Payments</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>

            {{-- Order Status --}}
            <select name="status"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-morocco-blue focus:border-morocco-blue transition">
                <option value="">All Statuses</option>
                @foreach (['pending', 'paid', 'shipped', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Button --}}
            <button type="submit"
                class="flex items-center gap-2 bg-morocco-blue text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-morocco-blue transition">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>

        {{-- Refresh Button --}}
        <div class="flex flex-wrap gap-3">
            <x-button.primary-button href="{{ route('admin.orders.index') }}" icon="fas fa-arrows-rotate" color="gray">
                Refresh
            </x-button.primary-button>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-lg">
        <table class="w-full text-left border-collapse">
            <thead class="bg-morocco-ivory text-gray-700 text-sm uppercase tracking-wide sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3">Client</th>
                    <th class="px-6 py-3 text-center">Payment</th>
                    <th class="px-6 py-3 text-center">Total</th>
                    <th class="px-6 py-3 text-center">Date</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($orders as $order)
                    <tr class="border-t hover:bg-morocco-ivory transition">
                        <td class="px-6 py-4 font-medium">
                            {{ $order->client->user?->name ?? 'Guest' }}
                            <br>
                            <span class="text-xs text-gray-500">{{ $order->client->user?->email ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs
                                {{ $order->payment?->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($order->payment?->status ?? 'Pending') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold">{{ number_format($order->total_amount, 2) }} MAD</td>
                        <td class="px-6 py-4 text-center">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 text-center flex justify-center gap-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-morocco-blue hover:underline flex items-center gap-1">
                                <i class="fas fa-eye"></i> View
                            </a>

                            <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" onsubmit="return confirm('Delete order #{{ $order->id }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 flex items-center gap-1">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-8 italic">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection
