<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ClientCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'address'    => ['required', 'string', 'max:500'],
            'city_id'    => ['required', 'exists:cities,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ];
    }
}