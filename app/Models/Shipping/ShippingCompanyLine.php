<?php

namespace App\Models\Shipping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCompanyLine extends Model
{
    use SoftDeletes;

    protected $fillable = ['shipping_company_id', 'from_shipping_area_id', 'to_shipping_area_id', 'is_deliver_on_hand'];

    public function price()
    {
        return $this->hasMany(ShippingCompanyLinePrice::class)
            ->select('shipping_company_line_id', 'price', 'kg');
    }

    public function days()
    {
        return $this->hasMany(ShippingCompanyLineDay::class)
            ->select('shipping_company_line_id', 'day_of_week_id')
            ->with('dayName');
    }

    public function fromArea()
    {
        return $this->belongsTo(ShippingArea::class, 'from_shipping_area_id', 'id')
            ->select('id', 'place_type', 'place_id')
            ->with('area');
    }

    public function toArea()
    {
        return $this->belongsTo(ShippingArea::class, 'to_shipping_area_id', 'id')
            ->select('id', 'place_type', 'place_id')
            ->with('area');
    }

}
