<?php

namespace App\Models;

use App\Lib\Services\ImageUploader\UploadImage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'activation',
        'type_id',
        'user_id',
        'from',
        'to',
        'discount_value',
        'discount_type',
        'bulk_price',
        'retail_price',
        'deleted_at',
        'has_end_date',
        'total_purchased_items'
    ];

    public function initializeOfferFields($data)
    {
        $this->name_ar = $data['name_ar'];
        $this->name_en = $data['name_en'];
        $this->description = $data['description'];
        $this->activation = $data['activation'];
        $this->user_id = $data['presenter_id'];
        $this->type_id = $data['type_id'];
        $this->discount_value = $data['discount_value'];
        $this->discount_type = $data['discount_type'];
        $this->total_price = $data['total_price'];
        $this->total_purchased_items = $data['total_purchased_items'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->bulk_price = $data['bulk_price'];
        $this->retail_price = $data['retail_price'];
        $this->max_usage_count = $data['max_usage_count'];
        $this->image = UploadImage::uploadImageToStorage($data['image'], 'offers');

    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $q
     * @return Builder
     */
    public function scopeActive($q): Builder
    {
        return $q->whereDate('to', '>=', Carbon::today()->toDateString())->orWhere('has_end_date', false);
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
