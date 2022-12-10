<?php

namespace App\Models\Shipping;

use App\Models\SellerRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCompany extends Model
{
    use SoftDeletes;

    protected $fillable = ['name_ar', 'name_en', 'image', 'email', 'activation'];

    public function locations()
    {
        return $this->hasMany(ShippingCompanyLocation::class)
            ->select('id', 'address', 'latitude', 'longitude', 'shipping_company_id')
            ->with('phones');
    }

    public function lines()
    {
        return $this->hasMany(ShippingCompanyLine::class)
            ->select('id', 'is_deliver_on_hand', 'shipping_company_id', 'from_shipping_area_id', 'to_shipping_area_id', 'from_name', 'to_name')
            ->with(['price', 'days', 'fromArea', 'toArea']);
    }

    public function rate()
    {
        return $this->morphMany(SellerRate::class, 'rated');
    }

}
