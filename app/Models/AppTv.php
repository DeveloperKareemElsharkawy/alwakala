<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppTv extends Model
{
    protected $fillable = [
        'web_image',
        'mobile_image',
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'item_id',
        'items_ids',
        'home_section_id',
        'type_id',
        'expiry_date',
        'category_id'
    ];

    public function type()
    {
        return $this->belongsTo(AppTvType::class, 'item_type', 'id');
    }

    public function app()
    {
        return $this->belongsTo(App::class, 'app_id', 'id');
    }

    public function category_item()
    {
        return $this->belongsTo(Category::class,'item_id')->select('id', 'name_ar', 'name_en');
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->select('id', 'name_ar', 'name_en');
    }

    public function store()
    {
        return $this->belongsTo(Store::class,'item_id')->select('id', 'name');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'item_id')->select('id', 'name');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class,'item_id');
    }
}
