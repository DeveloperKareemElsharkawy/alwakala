<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'iso',
        'name_ar',
        'name_en',
        'country_code',
        'phone_code',
    ];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

}
