<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $table = 'conversions';
    protected $fillable = [
        'user_id', 'from_coin', 'to_coin', 'from_currency_id', 'to_currency_id', 'from_amount', 'to_amount', 'rate', 'status'
    ];
}
