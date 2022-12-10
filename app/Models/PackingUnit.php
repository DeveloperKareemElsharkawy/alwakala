<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackingUnit extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar'
    ];
}
