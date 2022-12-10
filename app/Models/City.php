<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'state_id',
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class)->with('state');
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
