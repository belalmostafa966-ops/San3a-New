<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = ['job_id', 'opened_by', 'reason', 'resolution', 'status'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }
}
