@extends('layouts.app')

@section('title', 'Order Success | ' . config('app.name'))

@section('content')
<section class="bg-morocco-ivory py-16 text-center">
    <h1 class="text-4xl font-bold text-green-600">Payment Successful!</h1>
    <p class="mt-4 text-gray-700">Thank you for your purchase. Your order is being processed.</p>
    <a href="{{ route('products.index') }}" class="mt-6 inline-block px-6 py-3 bg-morocco-red text-white rounded-xl shadow hover:bg-red-700 transition">
        Continue Shopping
    </a>
</section>
@endsection
