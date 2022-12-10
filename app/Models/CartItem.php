<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'packing_unit_id',
        'product_store_id',
        'cart_id',
        'user_id',
        'store_id',
        'product_id',
        'color_id',
        'basic_unit_count',
        'quantity',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function product_store(): BelongsTo
    {
        return $this->belongsTo(ProductStore::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
