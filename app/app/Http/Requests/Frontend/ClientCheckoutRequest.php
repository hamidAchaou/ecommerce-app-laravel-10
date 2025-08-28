<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ClientCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // only authenticated users
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'country_id' => 'required|exists:countries,id',
        ];
    }
}