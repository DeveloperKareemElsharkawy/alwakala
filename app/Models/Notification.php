<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['title', 'body', 'is_read', 'image', 'item_id', 'item_type', 'user_id'];
}
