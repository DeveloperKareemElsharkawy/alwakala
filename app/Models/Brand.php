<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['name_ar','name_en', 'activation', 'image'];

    public function categoryBrand()
    {
        return $this->belongsToMany(Category::class, 'brand_category')
            ->select('categories.id', 'categories.name_ar', 'categories.name_en');
    }

    public function brand_category()
    {
        return $this->belongsToMany(Category::class, 'brand_category');
    }
}
