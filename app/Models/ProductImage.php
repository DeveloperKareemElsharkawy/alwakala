<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'image',
        'color_id',
        'product_id'
    ];

    public function color()
    {
        return $this->belongsTo(Color::class)->select('id', 'name_en as name', 'hex');
    }
}
