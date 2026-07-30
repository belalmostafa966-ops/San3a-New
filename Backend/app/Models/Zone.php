<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name', 'city', 'governorate', 'lat', 'lng'];

    public function craftsmen()
    {
        return $this->belongsToMany(CraftsmanProfile::class, 'craftsman_zones', 'zone_id', 'craftsman_id');
    }
}
