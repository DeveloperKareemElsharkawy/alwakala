<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['quantity', 'transaction_id', 'transaction_type_id', 'packing_unit_product_store_id'];
    /**
     * @var mixed
     */
}
