<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'activation', 'area_id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

}
