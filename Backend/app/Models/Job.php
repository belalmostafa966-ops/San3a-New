<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ملحوظة: الجدول اسمه jobs_records مش jobs عشان يتعارض مع جدول
 * الـ queue الافتراضي بتاع Laravel. الموديل هنا بردو مسميناه Job
 * بس لو حصل تعارض غريب مع Laravel Queue Job في حاجة تانية غيّري
 * اسم الموديل لـ JobRecord.
 */
class Job extends Model
{
    use HasFactory;

    protected $table = 'jobs_records';


    protected $fillable = [
        'job_request_id', 'craftsman_id', 'client_id', 'status',
        'started_at', 'otp_code', 'otp_confirmed_at', 'completed_at',
        'cancelled_at', 'cancel_reason', 'cancelled_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'otp_confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function jobRequest()
    {
        return $this->belongsTo(JobRequest::class);
    }

    public function craftsman()
    {
        return $this->belongsTo(CraftsmanProfile::class, 'craftsman_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function report()
    {
        return $this->hasOne(JobReport::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function gpsPings()
    {
        return $this->hasMany(GpsPing::class);
    }

    public function statusEvents()
    {
        return $this->hasMany(JobStatusEvent::class);
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    public function logStatusEvent(string $eventType, array $meta = []): void
    {
        $this->statusEvents()->create([
            'event_type' => $eventType,
            'meta_json' => $meta,
        ]);
    }

    public function isHanging(int $hoursThreshold = 3): bool
    {
        return $this->status === 'in_progress'
            && $this->started_at
            && $this->started_at->diffInHours(now()) >= $hoursThreshold
            && is_null($this->completed_at);
    }
}
