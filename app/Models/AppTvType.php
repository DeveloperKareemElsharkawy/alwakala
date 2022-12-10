<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppTvType extends Model
{
    public $timestamps = false;

    protected $fillable = ['type_en','type_ar'];
}
