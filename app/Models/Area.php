<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'activation', 'city_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
