<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityStore extends Model
{
    protected $table = 'city_store';
    protected $fillable = ['city_id', 'store_id', 'fees'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
