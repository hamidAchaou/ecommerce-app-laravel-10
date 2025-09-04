<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Repository for order data access.
     *
     * @var OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * Constructor with dependency injection.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;

        // Ensure only authenticated users access orders
        $this->middleware('auth');
    }

    /**
     * Display paginated orders for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Use repository to fetch orders with relations
        $orders = $this->orderRepository->getAllPaginate(
            filters: ['client_id' => $user->client?->id],
            with: ['payment', 'orderItems.product'],
            perPage: 10
        );

        return view('frontend.orders.index', compact('orders'));
    }

    /**
     * Show details of a single order.
     *
     * @param int $orderId
     * @param Request $request
     * @return \Illuminate\View\View
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $orderId, Request $request)
    {
        $user = $request->user();

        // Retrieve order with relations
        $order = $this->orderRepository->find($orderId, ['client', 'payment', 'orderItems.product']);

        // Authorization: ensure the current user owns this order
        if (!$order || $order->client?->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this order');
        }

        return view('frontend.orders.show', compact('order'));
    }
}