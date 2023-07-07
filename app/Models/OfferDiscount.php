<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_from',
        'price_to',
        'discount_value',
    ];
}
