<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'sent_address',
        'coin',
        'amount',
        'image_path',
        'status',
        'action_status_id',
    ];

    protected $casts = [
        // use higher precision for crypto amounts
        'amount' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actionStatus()
    {
        return $this->belongsTo(ActionStatus::class, 'action_status_id');
    }
}
