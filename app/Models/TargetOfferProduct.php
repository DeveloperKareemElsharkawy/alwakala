<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetOfferProduct extends Model
{
    use HasFactory;
    protected $table = 'target_offer_products';
    protected $fillable = [
        'target_id','product_id'
    ];
}
