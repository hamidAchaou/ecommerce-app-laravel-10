<?php

namespace App\Listeners;

use App\Services\Frontend\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;

class MergeGuestCart
{
    public function __construct(protected CartService $cartService) {}

    public function handle(Login|Registered $event): void
    {
        $this->cartService->mergeGuestCartToUser();
    }
}