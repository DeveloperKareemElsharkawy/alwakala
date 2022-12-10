<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryStore extends Model
{
    use HasFactory;
    protected $table = 'category_store';
    protected $fillable = ['store_id', 'category_id'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id')->select('id', 'name_ar', 'name_en');
    }

    public function mainCategory()
    {
        return $this->hasOne(Category::class, 'id', 'category_id')->whereNull('category_id')->select('id', 'name_ar', 'name_en');
    }
}
