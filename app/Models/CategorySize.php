<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySize extends Model
{
    protected $table = 'category_size';
    protected $fillable = ['size_id', 'category_id'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id')->select('id', 'name_ar', 'name_en');
    }
}
