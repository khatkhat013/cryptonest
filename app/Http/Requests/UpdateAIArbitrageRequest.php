<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAIArbitrageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'plan_name' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s\-_]*$/',
            'amount' => 'nullable|numeric|min:0.00000001|max:999999999.99999999',
            'quantity' => 'nullable|numeric|min:0.00000001|max:999999999.99999999',
            'profit_rate' => 'nullable|numeric|min:0|max:100',
            'daily_revenue_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|string|in:active,inactive,completed,paused',
            'duration_hours' => 'nullable|integer|min:1|max:87600', // up to 10 years in hours
            'duration_days' => 'nullable|integer|min:1|max:3650',
            'started_at' => 'nullable|date_format:Y-m-d H:i|before_or_equal:now',
            'completed_at' => 'nullable|date_format:Y-m-d H:i|after_or_equal:started_at',
        ];
    }

    public function messages(): array
    {
        return [
            'plan_name.regex' => 'Plan name contains invalid characters.',
            'amount.numeric' => 'Amount must be a valid number.',
            'profit_rate.numeric' => 'Profit rate must be a valid number.',
            'profit_rate.max' => 'Profit rate cannot exceed 100%.',
            'status.in' => 'Invalid status selected.',
            'duration_hours.integer' => 'Duration must be a whole number.',
            'started_at.date_format' => 'Started at must be a valid date format.',
            'completed_at.after_or_equal' => 'Completed at must be after or equal to started at.',
        ];
    }
}
