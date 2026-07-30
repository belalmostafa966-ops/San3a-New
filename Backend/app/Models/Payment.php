<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['job_id', 'payer_id', 'amount', 'method', 'status', 'gateway_ref', 'paid_at'];

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }
}