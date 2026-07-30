<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitFeeHold extends Model
{
    protected $fillable = ['job_request_id', 'client_id', 'amount', 'status'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function jobRequest()
    {
        return $this->belongsTo(\App\Models\JobRequest::class, 'job_request_id');
    }
}