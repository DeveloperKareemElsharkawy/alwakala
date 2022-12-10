<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = ['role_id', 'store_id', 'user_id'];

    public function favProducts()
    {
        return $this->belongsToMany(Product::class, 'favourite_seller_products');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function storeType()
    {
        return $this->belongsTo(StoreType::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'store_category_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function SellerRate()
    {
        return $this->morphMany(SellerRate::class, 'rater');
    }
}
