<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySize extends Model
{
    protected $table = 'category_size';
    protected $fillable = ['size_id', 'category_id'];

}
