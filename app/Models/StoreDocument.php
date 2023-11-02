<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreDocument extends Model
{
    use HasFactory;

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if(isset($this->image)){
            return config('filesystems.aws_base_url') . $this->image;
        }else{
            return \URL::asset('/admin/assets/images/users/48/empty.png');
        }
    }
}
