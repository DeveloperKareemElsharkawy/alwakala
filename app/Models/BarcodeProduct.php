<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'barcode',
        'color_id',
        'product_id'
    ];

    protected $table = 'barcode_product';

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'color_id', 'color_id')
            ->select('id', 'image', 'color_id');
    }

    public function stock()
    {
        return $this->hasMany(ProductStoreStock::class, 'color_id', 'color_id')
            ->select('id', 'stock', 'reserved_stock', 'available_stock',  'sold', 'returned', 'size_id', 'color_id')->with('size');
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
