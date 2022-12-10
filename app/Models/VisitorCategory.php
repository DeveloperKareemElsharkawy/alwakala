<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorCategory extends Model
{
    protected $table = 'visitor_category';
    protected $fillable = ['visitor_id', 'category_id'];
}
