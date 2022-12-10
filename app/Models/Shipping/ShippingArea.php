<?php

namespace App\Models\Shipping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingArea extends Model
{
    use SoftDeletes;

    protected $fillable = ['place_id', 'place_type'];

    public function area()
    {
        return $this->morphTo(__FUNCTION__, 'place_type', 'place_id')
            ->select('id', 'name_ar', 'name_en');
    }


}
