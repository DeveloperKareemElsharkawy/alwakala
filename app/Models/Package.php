<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable =['name_ar','name_en','type','price','product_limitation','period','description'];
    protected $table='packages';
    use HasFactory;
}
