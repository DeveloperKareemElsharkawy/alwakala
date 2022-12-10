<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    // protected $table = 'order_status_history';
    protected $fillable = [
        'order_status_id',
        'order_id',
        'order_product_id',
        'store_id',
    ];
}
