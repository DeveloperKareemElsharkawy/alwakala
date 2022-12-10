<?php

namespace App\Models\Shipping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCompanyLocationPhone extends Model
{
    use SoftDeletes;
    protected $fillable = ['shipping_company_location_id', 'phone'];
}
