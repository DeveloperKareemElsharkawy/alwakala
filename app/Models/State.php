<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'activation', 'region_id'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function stateCountry()
    {
        return $this->hasOneThrough(
            Country::class,
            Region::class
        );
    }
}
