<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = ['craftsman_id', 'amount', 'method', 'status', 'requested_at', 'processed_at'];

    public function craftsman()
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }
}
