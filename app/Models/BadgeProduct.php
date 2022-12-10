<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeProduct extends Model
{
    use HasFactory;

    protected $table = 'badge_product';
    protected $fillable = ['badge_id', 'store_id'];
}
