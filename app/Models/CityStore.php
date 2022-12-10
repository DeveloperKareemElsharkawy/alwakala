<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityStore extends Model
{
    protected $table = 'city_store';
    protected $fillable = ['city_id', 'store_id', 'fees'];
}
