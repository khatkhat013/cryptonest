<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionStatus extends Model
{
    use HasFactory;

    protected $table = 'action_statuses';

    protected $fillable = ['name'];

    /**
     * Deposits that have this status
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
}
