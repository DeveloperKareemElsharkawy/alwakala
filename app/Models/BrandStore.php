<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandStore extends Model
{
    use HasFactory;
    protected $fillable = ['store_id', 'brand_id'];
    protected $table = 'brand_store';
}
