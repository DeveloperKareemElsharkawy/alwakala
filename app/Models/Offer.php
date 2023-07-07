<?php

namespace App\Models;

use App\Lib\Services\ImageUploader\UploadImage;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image',

        'discount_value',
        'discount_type',

        'type',
        'target',


        'is_active',
        'user_id',
        'store_id',

        'start_date',
        'start_time',

        'end_date',
        'end_time',

        'deleted_at',
    ];


    protected $appends = ['imageUrl'];


    public function getImageUrlAttribute(): string
    {
        return config('filesystems.aws_base_url') . $this->image;
    }


    /**
     * Scope a query to only include popular users.
     *
     * @param $q
     * @return Builder
     */
    public function scopeActive($q): Builder
    {
        return $q->whereDate('start_date', '>=', Carbon::today()->toDateString())->where('is_active', true);
    }


    public function initializeOfferProducts($products)
    {
        if (count($products) > 0) {
            foreach ($products as $product) {
                $this->products()->attach($product['id']);
            }
        }
    }

    public function initializeOfferStores($stores)
    {
        if (count($stores) > 0) {
            foreach ($stores as $store) {
                $this->stores()->attach($store['store_id']);
            }
        }
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_product')
            ->withTimestamps();
    }

    public function offers_products()
    {
        return $this->hasMany(OfferProduct::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'offer_store')
            ->withTimestamps();
    }

    public function getDiscountValue($order_price)
    {
        if ($this->discount_type == 1) {
            return $this->discount_value;
        } else if ($this->discount_type == 2) {
            return ($order_price / 100) * $this->discount_value;
        }
        return 0; // goods case
    }

    public function user()
    {
        return $this->belongsTo(User::class)->with('stores')
            ->select('id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(OfferType::class);
    }
}
