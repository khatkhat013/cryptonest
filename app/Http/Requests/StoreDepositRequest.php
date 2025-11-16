<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // Whitelist of allowed coins to prevent injection
        $allowedCoins = ['btc', 'eth', 'usdt', 'usdc', 'pyusd', 'doge', 'xrp'];
        
        return [
            'coin' => [
                'required',
                'string',
                'max:16',
                Rule::in($allowedCoins),
            ],
            'amount' => 'required|numeric|min:0.00000001|max:999999999.99999999',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'sent_address' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\-_.~]{0,255}$/', // Allow alphanumeric and common chars
        ];
    }

    public function messages(): array
    {
        return [
            'coin.in' => 'Invalid coin type selected.',
            'coin.required' => 'Coin type is required.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be greater than zero.',
            'image.image' => 'The image must be a valid image file.',
            'image.mimes' => 'The image must be jpeg, png, or jpg.',
            'image.max' => 'The image must not exceed 5MB.',
            'sent_address.regex' => 'Sent address contains invalid characters.',
        ];
    }
}
