<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbackLedger extends Model
{
    protected $table = 'cashback_ledger';
    protected $fillable = ['client_id', 'job_id', 'amount', 'expires_at', 'used_at'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }
}