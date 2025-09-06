<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderRepository $orderRepository) {}

    /**
     * Display all orders with filters, search, and sorting.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'payment_status', 'status', 'sort_by', 'sort_dir']);
        $orders = $this->orderRepository->getAllWithRelations($filters, 15);

        // dd($orders);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show a single order.
     */
    public function show(int $orderId)
    {
        $order = $this->orderRepository->findWithRelations($orderId);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Delete an order with confirmation.
     */
    public function destroy(int $orderId)
    {
        $order = $this->orderRepository->findWithRelations($orderId);

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', "Order #{$orderId} deleted successfully.");
    }
}