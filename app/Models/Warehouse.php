<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    // protected $with = ['city', 'type','user' ,'products'];

    protected $fillable = ['name_ar', 'name_en', 'activation', 'address_en', 'address_ar', 'store_type_id', 'city_id', 'latitude', 'longitude' , 'user_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(StoreType::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'warehouse_products' , 'warehouse_id', 'product_id' );
    }

    public function warehouse_products()
    {
        return $this->hasMany(WarehouseProduct::class);
    }
}
