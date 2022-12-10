<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id' , 'coupon_id'
    ];

    /**
     * retrieve coupon data with the user
     *
     * @return BelongsTo
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class)->with(['products' , 'brand']);
    }

    /**
     * retrieve product data with the user
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->with(['owner' , 'brand']);
    }
}
