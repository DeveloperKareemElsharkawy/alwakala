<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrderUnitDetails extends Model
{
    protected $fillable = ['packing_unit_id', 'size_id', 'quantity'];
    protected $table = 'product_order_unit_details';

    public function size()
    {
        return $this->belongsTo(Size::class)->select('id', 'size');
    }
}
