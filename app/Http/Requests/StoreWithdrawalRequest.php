<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        $isAuthorized = $this->user() !== null;
        \Log::info('StoreWithdrawalRequest authorize', ['authorized' => $isAuthorized, 'user_id' => $this->user()->id ?? null]);
        return $isAuthorized;
    }

    public function rules(): array
    {
        // Whitelist of allowed coins
        $allowedCoins = ['btc', 'eth', 'usdt', 'usdc', 'pyusd', 'doge', 'xrp'];

        return [
            'destination_address' => [
                'required',
                'string',
                'min:3',
                'max:255',
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
        \Log::info('StoreWithdrawalRequest prepareForValidation', [
            'raw_input' => $this->all()
        ]);
        
        // Trim and sanitize inputs
        $this->merge([
            'destination_address' => trim($this->destination_address),
            'coin' => strtolower(trim($this->coin)),
        ]);
        
        \Log::info('After merge/prepare', [
            'prepared_input' => $this->all()
        ]);
    }
}
