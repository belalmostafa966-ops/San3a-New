<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentityFingerprint extends Model
{
    protected $fillable = ['user_id', 'hashed_national_id', 'hashed_card_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function hash(string $value): string
    {
        return hash('sha256', $value);
    }
}
