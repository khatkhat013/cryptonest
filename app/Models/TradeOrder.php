<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeOrder extends Model
{
    protected $fillable = ['user_id','symbol','direction','purchase_quantity','purchase_price','initial_price','final_price','price_range_percent','delivery_seconds','profit_amount','payout','result','force_applied','meta'];
    protected $casts = ['meta' => 'array', 'force_applied' => 'boolean'];

    /**
     * Get the user that owns this trade order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
