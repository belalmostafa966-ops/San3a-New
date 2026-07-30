<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'profession_id',
        'baseline_price',
        'time_multiplier_json',
        'geo_multiplier_json',
        'platform_margin',
    ];

    protected $casts = [
        'baseline_price' => 'decimal:2',
        'platform_margin' => 'decimal:2',
        'time_multiplier_json' => 'array',   // JSON يتحول تلقائي لـ PHP array
        'geo_multiplier_json' => 'array',
    ];

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }
}
