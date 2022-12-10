<?php

namespace App\Models;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'activation',
        'priority',
        'category_id',
        'image',
    ];
    public $timestamps = true;


    public function sizes()
    {
        $lang = LangHelper::getDefaultLang(request());
        return $this->belongsToMany(Size::class);
    }
    public function brands()
    {
        $lang = LangHelper::getDefaultLang(request());
        return $this->belongsToMany(Brand::class)
            ->select('brands.id', "name_$lang", 'image');
    }

    public function slides()
    {
        return $this->hasMany(AppTv::class)
            ->select('id', 'web_image','mobile_image', 'item_id', 'item_type', 'category_id')
            ->with('type');
    }

    public function categories()
    {
        return $this->hasMany(Category::class)
            ->select('id', 'name_ar', 'name_en', 'category_id', 'image');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class)->with('categories');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function packing_unit()
    {
        return $this->belongsTo(PackingUnit::class, 'packing_unit_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
