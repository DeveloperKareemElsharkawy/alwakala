<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = ["name_ar","name_en","hex"];
    public function product_store_stocks()
    {
        return $this->hasMany(ProductStoreStock::class);
    }
}
