<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'app_en',
        'app_ar',
    ];
}
