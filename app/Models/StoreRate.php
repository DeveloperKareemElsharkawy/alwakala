<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreRate extends Model
{
    protected $table = 'store_ratings';
    protected $fillable = ['amount', 'review', 'image', 'store_id'];

}
