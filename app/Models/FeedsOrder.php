<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedsOrder extends Model
{
    protected $table='feeds_order';
    protected $fillable = [
        'order',
        'product_id',
    ];
}
