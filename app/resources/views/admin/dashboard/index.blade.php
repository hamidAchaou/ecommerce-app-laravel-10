@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Dashboard Overview</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500">Total Products</h3>
            <p class="text-2xl font-bold">{{ $stats['total_products'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500">Categories</h3>
            <p class="text-2xl font-bold">{{ $stats['total_categories'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500">Orders</h3>
            <p class="text-2xl font-bold">{{ $stats['total_orders'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500">Revenue</h3>
            <p class="text-2xl font-bold">${{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4">Products by Category</h2>
            <canvas id="productsByCategory"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4">Monthly Sales</h2>
            <canvas id="monthlySales"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const productsByCategory = new Chart(
        document.getElementById('productsByCategory'),
        {
            type: 'pie',
            data: {
                labels: @json($categories->pluck('name')),
                datasets: [{
                    data: @json($categories->pluck('products_count')),
                    backgroundColor: ['#6366F1','#10B981','#F59E0B','#EF4444','#3B82F6'],
                }]
            }
        }
    );

    const monthlySales = new Chart(
        document.getElementById('monthlySales'),
        {
            type: 'bar',
            data: {
                labels: @json(array_keys($monthlySales)),
                datasets: [{
                    label: 'Sales ($)',
                    data: @json(array_values($monthlySales)),
                    backgroundColor: '#6366F1'
                }]
            }
        }
    );
</script>
@endpush
