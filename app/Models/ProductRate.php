<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRate extends Model
{
    protected $table = 'product_ratings';
    protected $fillable = ['amount', 'review', 'image', 'product_id'];

}
