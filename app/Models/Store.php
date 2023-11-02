<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'licence',
        'address',
        'landing_number',
        'mobile',
        'latitude',
        'longitude',
        'building_no',
        'landmark',
        'main_street',
        'side_street',
        'is_store_has_delivery',
        'user_id',
        'store_type_id',
        'city_id',
        'description',
        'is_verified',
        'qr_code',
        'qr_code_image',
        'parent_id',
        'store_profile_id',
        'is_main_branch'
    ];

    public function coverAreas()
    {
        return $this->hasMany(CityStore::class);
    }

    protected $appends = ['image_url' , 'cover_url' , 'licence_url'];

    public function getImageUrlAttribute()
    {
        if(isset($this->logo)){
            return config('filesystems.aws_base_url') . $this->logo;
        }else{
            return \URL::asset('/admin/assets/images/users/48/empty.png');
        }
    }

    public function getCoverUrlAttribute()
    {
        if(isset($this->cover)){
            return config('filesystems.aws_base_url') . $this->cover;
        }else{
            return \URL::asset('/admin/assets/images/users/48/empty.png');
        }
    }

    public function getLicenceUrlAttribute()
    {
        if(isset($this->licence)){
            return config('filesystems.aws_base_url') . $this->licence;
        }else{
            return \URL::asset('/admin/assets/images/users/48/empty.png');
        }
    }
    public function getBadge() {
        $badge = '';
        if($this->activation == true && $this->is_verified == true){
            $badge = '<span class="badge bg-success-subtle text-success">Active</span>';
        }
        if($this->activation == false && $this->is_verified == true){
            $badge = '<span class="badge bg-danger-subtle text-danger">Inactive</span>';
        }
        if($this->activation == false && $this->is_verified == false){
            $badge = '<span class="badge bg-warning-subtle text-warning">Pending</span>';
        }
        if($this->activation == true && $this->is_verified == false){
            $badge = '<span class="badge bg-warning-subtle text-warning">Pending</span>';
        }
        return $badge;
    }

    public function followers()
    {
        return $this->hasMany(FollowedStore::class , 'store_id' , 'id');
    }

    public function views()
    {
        return $this->hasMany(View::class , 'item_id' , 'id')->where('item_type','STORE');
    }

    public function identity()
    {
        return $this->hasOne(StoreDocument::class , 'store_id' , 'id')->where('type','identity');
    }

    public function text_card()
    {
        return $this->hasOne(StoreDocument::class , 'store_id' , 'id')->where('type','text_card');
    }

    public function categories()
    {
        return $this->hasMany(CategoryStore::class)
            ->select('store_id', 'category_id')
            ->with('category');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->select('id', 'name', 'email', 'mobile', 'activation');
    }

    public function storeImages()
    {
        return $this->hasMany(StoreImage::class)->select('id', 'store_id', 'image');
    }

    public function storeCategories()
    {
        return $this->belongsToMany(Category::class, 'category_store')
            ->withTimestamps();
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_store');
    }

    public function mainBranch()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function branches()
    {
        return $this->hasMany(self::class, 'parent_id')->where('archive' , false);
    }

    public function initializeStoreData($data)
    {
        $this->seller_id = $data['seller_id'];
        $this->name = $data['name'];
        $this->store_type_id = $data['store_type_id'];
        $this->category_id = $data['category_id'];
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
        $this->landing_number = $data['landing_number'];
        $this->mobile = $data['mobile'];
        $this->address = $data['building_no'] . $data['main_street'];
        $this->building_no = $data['building_no'];
        $this->landmark = $data['landmark'];
        $this->main_street = $data['main_street'];
        $this->side_street = $data['side_street'];
        $this->city_id = $data['city_id'];
        $this->is_store_has_delivery = $data['is_store_has_delivery'];
        $this->is_main_branch = $data['is_main_branch'];

    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->select('products.id', 'name', 'product_store.price', 'product_store.net_price', 'product_store.discount')
            ->groupBy(['products.id', 'product_store.store_id', 'product_store.product_id', 'product_store.price', 'product_store.net_price', 'product_store.discount'])
//            ->with('productPrice')
            ->with('productImage')
            ->limit(4);
    }

    public function allProducts()
    {
        return $this->belongsToMany(Product::class);
    }

    public function productStores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductStore::class);
    }

    public function productsForFeeds()
    {
        return $this->belongsToMany(Product::class)
            ->select('products.id', 'name', 'category_id')
            ->groupBy(['products.id', 'product_store.store_id', 'product_store.product_id'])
            ->with('productPrice')
            ->with('productImage')
            ->limit(7);
    }


    public function productsForFeedsV2()
    {
        return $this->belongsToMany(Product::class);
    }

    public function type()
    {
        return $this->belongsTo(StoreType::class, 'store_type_id')
            ->select('id', 'name_ar', 'name_en');
    }

    public function city()
    {
        return $this->belongsTo(City::class)->select('id', 'name_ar', 'name_en', 'state_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function SellerRate()
    {
        return $this->morphMany(SellerRate::class, 'rated')
            ->groupBy([
                'seller_rates.rated_id',
                'seller_rates.id',
                'seller_rates.review',
                'seller_rates.rater_id',
                'seller_rates.images',
                'seller_rates.created_at',
                'users.name',
                'users.image'
            ])
            ->join('users', 'users.id', '=', 'seller_rates.rater_id')
            ->select(
                'seller_rates.id',
                'review',
                'rated_id',
                'seller_rates.images',
                'rater_id as rater_id',
                'users.name as rated_by',
                'users.image as rater_image',
                'seller_rates.created_at',
                DB::raw('ROUND (AVG( rate ), 0) AS rate'
                ));
    }

    public function openHours()
    {
        return $this->hasMany(StoreOpeningHour::class)
            ->select('store_id', 'days_of_week_id', 'open_time', 'close_time')
            ->with('day');
    }

    public function SellerFavorite()
    {
        return $this->morphMany(SellerFavorite::class, 'favorited');
    }

    public function FollowedStore()
    {
        return $this->hasMany(FollowedStore::class, 'store_id', 'id');
    }

    public function shoppingCartProduct()
    {
        return $this->hasMany(ProductShoppingCart::class)
            ->join('colors', 'colors.id', '=', 'product_shopping_cart.color_id')
            ->join('products', 'products.id', '=', 'product_shopping_cart.product_id')
            ->select(
                'product_shopping_cart.product_id',
                'products.name',
                'product_shopping_cart.store_id',
                'product_shopping_cart.purchased_item_count',
                'product_shopping_cart.packing_unit_id',
                'product_shopping_cart.size_id',
                'product_shopping_cart.item_price',
                'product_shopping_cart.total_price',
                'product_shopping_cart.basic_unit_count',
                'colors.name_ar as color_ar',
                'colors.name_en as color_en'
            );
    }

    public function shoppingCart()
    {
        return $this->hasMany(ShoppingCart::class, 'store_id');
    }

    public function storeOpeningHours(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StoreOpeningHour::class);
    }

    public function storeSettings()
    {
        return $this->hasOne(StoreSetting::class);
    }

    /**
     * The roles that belong to Many the Products.
     */
    public function badges(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Badge::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'store_id')
            ->select('id', 'order_price', 'total_price', 'discount', 'address', 'store_id', 'delivery_date', 'number');
    }


    public function ScopeDistance($query, $latitude, $longitude, $distance)
    {
        // This will calculate the distance in km
        // if you want in miles use 3959 instead of 6371
        $raw = \DB::raw('6371 * acos(cos(radians(' . $latitude . ')) * cos(radians(stores.latitude)) * cos(radians(stores.longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(stores.latitude))) as distance');
        return $query->selectRaw($raw)
            ->havingRaw('distance < ' . $distance);
    }

}
