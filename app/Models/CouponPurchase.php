<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id' , 'coupon_id'
    ];

    /**
     * retrieve orders data with the user
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->with('user');
    }

    /**
     * retrieve coupon data with the user
     *
     * @return BelongsTo
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class)->with(['products' , 'brand']);
    }
}
