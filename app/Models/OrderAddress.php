<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderAddress extends Model
{
//    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'building_no',
        'landmark',
        'main_street',
        'side_street',
        'city_id',
        'mobile ',
        'is_default ',
        'mobile'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

}
