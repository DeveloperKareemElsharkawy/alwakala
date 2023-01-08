<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponDiscount extends Model
{
    use HasFactory;


    protected $casts = [
        'amount_from' => 'double',
        'amount_to' => 'double',
        'discount' => 'double',
    ];

}
