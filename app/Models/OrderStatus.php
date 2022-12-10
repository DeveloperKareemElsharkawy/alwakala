<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable = [
        'status_ar',
        'status_en'
    ];
    protected $table = 'order_statuses';
    public $timestamps = false;
}
