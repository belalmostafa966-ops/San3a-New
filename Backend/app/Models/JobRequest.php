<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'profession_id', 'description', 'zone_id',
        'address', 'preferred_time', 'status', 'visit_fee_status',
    ];

    protected $casts = [
        'preferred_time' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function proposals()
    {
        return $this->hasMany(JobProposal::class);
    }

    public function job()
    {
        return $this->hasOne(Job::class);
    }

    public function isVisitFeePaid(): bool
    {
        return $this->visit_fee_status === 'held';
    }
}
