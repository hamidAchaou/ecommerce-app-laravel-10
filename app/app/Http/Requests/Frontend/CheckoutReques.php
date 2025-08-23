<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can checkout
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'max:255'],
            'payment_method'   => ['required', 'string', 'in:card,paypal,cash'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ];
    }
}