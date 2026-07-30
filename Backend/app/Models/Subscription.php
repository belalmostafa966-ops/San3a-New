<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['craftsman_id', 'plan_id', 'starts_at', 'ends_at', 'status'];

    public function craftsman()
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}