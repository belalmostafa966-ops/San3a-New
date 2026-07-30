<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'craftsman_id',
        'zone_id',
        'starts_at',
        'ends_at',
        'amount_paid',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function craftsman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
}
