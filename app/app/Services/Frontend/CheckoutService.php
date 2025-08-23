<?php

namespace App\Services\Frontend;

use App\Services\Frontend\CartService;
use App\Services\Frontend\ClientService;
use App\Services\Frontend\OrderService;

class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private ClientService $clientService,
        private OrderService $orderService
    ) {}

    /**
     * Get cart data for checkout
     */
    public function getCartData(): array
    {
        $items = $this->cartService->getItems();

        return [
            'items'    => $items->values(),
            'total'    => $items->sum(fn($i) => $i['price'] * $i['quantity']),
            'count'    => $items->sum(fn($i) => $i['quantity']),
            'is_empty' => $items->isEmpty(),
        ];
    }

    /**
     * Get authenticated client
     */
    public function getClient()
    {
        return $this->clientService->getAuthenticatedClient();
    }

    /**
     * Place order
     */
    public function placeOrder(array $data)
    {
        $client = $this->getClient();
        if (!$client) {
            throw new \Exception("User must be logged in to place an order");
        }
    
        // Convert items Collection to array
        $items = $this->cartService->getItems()->toArray();
    
        return $this->orderService->createOrder($client, $items, $data);
    }
    
}