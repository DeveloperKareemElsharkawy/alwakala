<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    protected $fillable = [
        'product_id',
        'store_id',
        'quantity',
        'price'
    ];

    public function products() {
        return $this->belongsTo(Product::class,'product_id', 'id');
    }


    public function stores() {
        return $this->belongsTo(Store::class,'store_id', 'id');
    }
}
