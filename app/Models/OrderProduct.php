<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_products';
    protected $fillable = [
        'packing_unit_id',
        'order_id',
        'product_id',
        'purchased_item_count',
        'size_id',
        'item_price',
        'color_id',
        'total_price',
        'basic_unit_count',
        'status_id',
        'store_id',
        'added_to_inventory'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)
            ->with('productImage')
            ->select('id', 'name');
    }

    public function color(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Color::class)->select('id', 'name_en','name_ar', 'hex');
    }

    public function size(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function unit_details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductOrderUnitDetails::class);
    }

    public function last_status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
