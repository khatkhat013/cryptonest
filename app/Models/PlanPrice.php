<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPrice extends Model
{
    use HasFactory;

    protected $table = 'plan_prices';

    protected $fillable = [
        'admin_id',
        'plan_id',
        'plan_name',
        'plan_price',
        'plan_duration',
        'plan_description',
        'crypto_screenshot',
        'mobile_screenshot',
        'payment_method',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
