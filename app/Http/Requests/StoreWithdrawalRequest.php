<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // Whitelist of allowed coins
        $allowedCoins = ['btc', 'eth', 'usdt', 'usdc', 'pyusd', 'doge', 'xrp'];

        return [
            'destination_address' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\-_.~:@\/]{10,255}$/', // Allow crypto address formats
            ],
            'amount' => 'required|numeric|min:0.00000001|max:999999999.99999999',
            'coin' => [
                'required',
                'string',
                'max:16',
                Rule::in($allowedCoins),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'destination_address.required' => 'Destination address is required.',
            'destination_address.max' => 'Destination address is too long.',
            'destination_address.regex' => 'Destination address format is invalid.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be greater than zero.',
            'coin.required' => 'Coin type is required.',
            'coin.in' => 'Invalid coin type selected.',
        ];
    }

    protected function prepareForValidation()
    {
        // Trim and sanitize inputs
        $this->merge([
            'destination_address' => trim($this->destination_address),
            'coin' => strtolower(trim($this->coin)),
        ]);
    }
}
