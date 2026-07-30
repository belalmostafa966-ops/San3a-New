<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CraftsmanProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profession_id',
        'years_experience',
        'bio',
        'jobs_completed_count',
        'verification_tier',
        'rating_avg',
        'behavior_score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'craftsman_zones', 'craftsman_id', 'zone_id');
    }

    public function verificationDocuments()
    {
        return $this->hasMany(VerificationDocument::class, 'user_id', 'user_id');
    }

    /**
     * منطق الـ PDF: يتحول لـ verified تلقائي بعد 3 شغلانات ناجحة + سلوك >= 5
     */
    public function checkAndUpgradeVerificationTier(): void
    {
        if ($this->jobs_completed_count >= 3 && $this->behavior_score >= 5 && $this->verification_tier === 'basic') {
            $this->update(['verification_tier' => 'verified']);
            // TODO: هنا تنادي notification service تبعت للصنايعي طلب رفع الفيش والتشبيه
        }
    }

    public function isBehaviorBlocked(): bool
    {
        return $this->behavior_score < 5;
    }
}
