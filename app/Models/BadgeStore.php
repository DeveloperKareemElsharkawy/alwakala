<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeStore extends Model
{
    use HasFactory;

    protected $table = 'badge_store';
    protected $fillable = ['badge_id', 'store_id'];
}
