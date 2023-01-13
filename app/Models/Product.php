<?php

namespace App\Models;

use App\Models\Scopes\Consumer\Products\ActiveProductScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'activation',
        'category_id',
        'owner_id',
        'channel',
        'basic_unit_id',
        'consumer_price',
        'material_id',
        'material_rate',
        'shipping_method_id',
        'policy_id',
        'youtube_link',
    ];


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ActiveProductScope());
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->select('id', 'name_en', 'name_ar');
    }

    public function shipping_method()
    {
        return $this->belongsTo(ShippingMethod::class)->select('id', 'name_en', 'name_ar');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id')->select('id', 'name_ar', 'name_en');

    }

    public function material_2()
    {
        return $this->belongsTo(Material::class, 'material_id_2')->select('id', 'name_ar', 'name_en');
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->select('id', 'name_en', 'name_ar');
    }

    public function basicUnit()
    {
        return $this->belongsTo(PackingUnit::class)->select('id', 'name_ar', 'name_en');

    }

    public function owner()
    {
        return $this->belongsTo(User::class)->select('id', 'name');

    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        return $this->hasOne(ProductImage::class)->select('product_id', 'image', 'color_id');
    }

    public function productImage()
    {
        return $this->hasOne(ProductImage::class)->select(DB::raw("image,CONCAT('" . config('filesystems.aws_base_url') . "',image) AS image_full,product_id"));
        return $this->hasOne(ProductImage::class)->select('product_id', 'image');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function favActor()
    {
        return $this->belongsToMany(User::class, 'favourite_products');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_products');
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_products');
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function warehouse_products()
    {
        return $this->hasMany(WarehouseProduct::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'product_store')
            ->withPivot('stock', 'sales_price');
    }

    public function SellerRate()
    {
        return $this->morphMany(SellerRate::class, 'rated');
    }

    public function productStore()
    {
        return $this->hasOne(ProductStore::class);
    }

    public function packingUnitProduct()
    {
        return $this->hasOne(PackingUnitProduct::class);
    }

    public function sellers()
    {
        return $this->hasMany(ProductStore::class);
    }

//    public function productPackingUnitBasic()
//    {
//        return $this->hasOne(PackingUnitProduct::class)
//            ->where('basic_unit_count', 1)
//            ->select('product_id', 'purchase_price');
//    }

//    public function productPrice()
//    {
//        return $this->hasMany(Bundle::class)
//            ->select('product_id', 'store_id',
//                DB::raw("CASE WHEN min(bundles.price) != max(bundles.price) THEN CONCAT(min(bundles.price) , '-' , max(bundles.price)) WHEN min(bundles.price) IS NULL THEN '' ELSE min(bundles.price)::varchar END AS price_range"))
//            ->groupBy('bundles.product_id', 'bundles.store_id');
//    }

    public function SellerFavorite()
    {
        return $this->morphMany(SellerFavorite::class, 'favorited');
    }

    public function packingUnit()
    {
        return $this->belongsToMany(PackingUnit::class, 'packing_unit_product', 'product_id', 'packing_unit_id');
    }

    public function packingUnitAttributes()
    {
        return $this->hasMany(PackingUnitProductAttribute::class, 'packing_unit_product_id');
    }

    public function bundles()
    {
        return $this->hasMany(Bundle::class);
    }

    public function barcodes()
    {
        return $this->hasMany(BarcodeProduct::class);
    }

    /**
     * The roles that belong to Many the Products.
     */
    public function badges(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Badge::class);
    }

    public function policy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Policy::class)->select('id', 'name_en', 'name_ar');
    }

    public function shipping(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id')->select('id', 'name_en', 'name_ar');
    }

    public function shippingMethod(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class)->select('id', 'name_en', 'name_ar');
    }
}
