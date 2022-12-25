<?php

namespace App\Models;

use App\Enums\Orders\AOrders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductStore extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'price',
        'net_price',
        'discount',
        'discount_type',
        'views',
        'store_id',
        'publish_app_at',
        'free_shipping',
        'is_purchased',
        'consumer_price',
        'barcode',
        'consumer_price_discount_type',
        'consumer_price_discount',
        'consumer_old_price',
        'barcode',
        'barcode_text'
    ];
    protected $table = 'product_store';

    public function productImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
            ->select('product_id', 'image');
    }

    public function productStoreStock()
    {
        return $this->hasMany(ProductStoreStock::class);
    }

    public function product_store_stock($color_id): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductStoreStock::class)
            ->where('available_stock', '>=', 0)
            ->where('color_id', $color_id);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
