<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'coin', 'destination_address', 'amount', 'status', 'action_status_id'
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
