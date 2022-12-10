<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = ['device_token', 'store_type_id'];

    public function visitorCategories()
    {
        return $this->belongsToMany(Category::class, 'visitor_category');
    }
}
