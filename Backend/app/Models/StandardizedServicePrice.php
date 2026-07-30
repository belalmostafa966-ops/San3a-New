<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandardizedServicePrice extends Model
{
    use HasFactory;

    // الأعمدة المسموح نملأها مباشرة (من الفورم أو الـ API)
    protected $fillable = [
        'profession_id',
        'title',
        'fixed_price',
        'description',
    ];

    // نحوّل fixed_price تلقائي لرقم عشري بخانتين
    protected $casts = [
        'fixed_price' => 'decimal:2',
    ];

    // العلاقة: كل سعر تابع لمهنة واحدة
    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }
}
