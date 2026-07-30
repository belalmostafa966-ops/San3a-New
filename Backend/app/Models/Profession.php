<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'icon'];

    public function craftsmanProfiles()
    {
        return $this->hasMany(CraftsmanProfile::class);
    }
}
