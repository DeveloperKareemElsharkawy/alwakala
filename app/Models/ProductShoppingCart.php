<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductShoppingCart extends Model
{
    protected $table = "product_shopping_cart";
    protected $fillable = [
        "shopping_cart_id",
        "purchased_item_count",
        "item_price" ,
        "total_price" ,
        "product_id" ,
        "packing_unit_id" ,
        "store_id" ,
        "basic_unit_count" ,
        "size_id" ,
        "color_id"
    ];
}
