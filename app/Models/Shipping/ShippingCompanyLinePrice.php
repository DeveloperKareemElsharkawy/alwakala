<?php

namespace App\Models\Shipping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCompanyLinePrice extends Model
{
    use SoftDeletes;
    protected $fillable = ['shipping_company_line_id', 'price', 'kg'];
}
