<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreType extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'name_cn',
        'name_tr',
    ];
}
