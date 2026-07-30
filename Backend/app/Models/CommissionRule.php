<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    protected $fillable = ['profession_id', 'min_percent', 'max_percent'];

    public function profession()
    {
        return $this->belongsTo(\App\Models\Profession::class);
    }
}
