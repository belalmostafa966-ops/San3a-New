<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    protected $fillable = ['job_id', 'installment_number', 'percentage', 'amount', 'status', 'due_at', 'paid_at'];

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }
}