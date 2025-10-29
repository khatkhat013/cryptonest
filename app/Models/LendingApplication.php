<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LendingApplication extends Model
{
    protected $fillable = [
        'user_id',
        'borrowing_amount',
        'credit_period',
        'housing_info_path',
        'income_proof_path',
        'bank_details_path',
        'identity_proof_path',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}