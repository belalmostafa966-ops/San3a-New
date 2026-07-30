<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobStatusEvent extends Model
{
    protected $fillable = ['job_id', 'event_type', 'meta_json'];

    protected $casts = [
        'meta_json' => 'array',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
