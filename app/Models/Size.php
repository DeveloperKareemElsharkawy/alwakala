<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = ['size', 'size_type_id'];

    // protected $with = ['categories' , 'sizeType'];

    public function sizeType()
    {
        return $this->belongsTo(SizeType::class)->select('id', 'type_en', 'type_ar');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->select('categories.id', 'categories.name_ar', 'categories.name_en');
    }

    public function product_store_stocks()
    {
        return $this->hasMany(ProductStoreStock::class);
    }
}
