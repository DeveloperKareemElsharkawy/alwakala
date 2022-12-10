<?php

namespace App\Models\Shipping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCompanyLocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['address', 'latitude', 'longitude', 'shipping_company_id'];

    public function phones()
    {
        return $this->hasMany(ShippingCompanyLocationPhone::class)
            ->select('shipping_company_location_id', 'phone');
    }
}
