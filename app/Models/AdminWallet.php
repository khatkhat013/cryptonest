<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'currency_id',
        'address',
        'coin_amount'
    ];

    /**
     * Get the admin that owns the wallet.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the currency of this wallet.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}