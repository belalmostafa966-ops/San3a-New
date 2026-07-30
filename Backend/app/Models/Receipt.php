<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = ['job_id', 'type', 'qr_payload', 'pdf_path'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
