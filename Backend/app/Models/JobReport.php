<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobReport extends Model
{
    protected $fillable = [
        'job_id', 'defect_description', 'work_done_description',
        'cost_breakdown_json', 'before_photos', 'after_photos', 'client_ack_at',
    ];

    protected $casts = [
        'cost_breakdown_json' => 'array',
        'before_photos' => 'array',
        'after_photos' => 'array',
        'client_ack_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
