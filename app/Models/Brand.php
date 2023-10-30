<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['name_ar','name_en', 'activation', 'image'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if(isset($this->image)){
            return config('filesystems.aws_base_url') . $this->image;
        }else{
            return null;
        }
    }

    public function categoryBrand()
    {
        return $this->belongsToMany(Category::class, 'brand_category')
            ->select('categories.id', 'categories.name_ar', 'categories.name_en');
    }

    public function Products()
    {
        return $this->hasMany(Product::class);
    }

    public function brand_category()
    {
        return $this->belongsToMany(Category::class, 'brand_category');
    }
}
