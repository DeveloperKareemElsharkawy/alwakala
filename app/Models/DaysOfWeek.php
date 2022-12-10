<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaysOfWeek extends Model
{
    protected $table = 'days_of_week';
    protected $fillable = ['name_ar', 'name_en'];
    public $timestamps = false;
}
