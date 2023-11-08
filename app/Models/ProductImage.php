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

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if(isset($this->image)){
            return config('filesystems.aws_base_url') . $this->image;
        }else{
            return \URL::asset('/admin/assets/images/users/48/empty.png');
        }
    }

    public function color()
    {
        return $this->belongsTo(Color::class)->select('id', 'name_en as name', 'hex');
    }
}
