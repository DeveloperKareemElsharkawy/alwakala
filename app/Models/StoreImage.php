<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreImage extends Model
{
    protected $table = 'store_image';
    protected $fillable = ['image', 'store_id'];
}
