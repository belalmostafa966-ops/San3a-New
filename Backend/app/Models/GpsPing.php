<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpsPing extends Model
{
    protected $fillable = ['job_id', 'craftsman_id', 'lat', 'lng', 'recorded_at'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
