<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['wallet_id', 'type', 'amount', 'balance_after', 'job_id', 'description', 'reference_id'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }
}
