<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'code', 'name', 'brand_id', 'type', 'seller_id',
         'quantity', 'unlimited',
          'active', 'start_date', 'end_date'
    ];

    /**
     * retrieve brand
     *
     * @return BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * retrieve user who owns the coupon
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * retrieve products that apply this coupon
     *
     * @return BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_products')
            ->withTimestamps();
    }

    /**
     * retrieve pivot table data
     *
     * @return HasMany
     */
    public function coupon_products(): HasMany
    {
        return $this->hasMany(CouponProduct::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(CouponDiscount::class);
    }

    public function change_active()
    {
        if ($this->active == 0) {
            $this->update([
                'active' => 1
            ]);
        } else {
            $this->update([
                'active' => 0
            ]);
        }
    }

    public function get_active()
    {
        switch ($this->active) {
            case '0':
                return 'غير مفعل';
                break;
            case '1':
                return 'مفعل';
                break;
        }
    }
}
