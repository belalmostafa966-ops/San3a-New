<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'rated_by',
        'rated_user_id',
        'direction',
        'score',
        'behavior_score',
        'comment',
    ];

    protected $casts = [
        'job_id' => 'integer',
        'rated_by' => 'integer',
        'rated_user_id' => 'integer',
        'score' => 'integer',
        'behavior_score' => 'integer',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function ratedBy()
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    public function ratedUser()
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }
}

