<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    //
    protected $fillable = ['website', 'facebook', 'instagram', 'whatsapp', 'twitter', 'pinterest', 'youtube', 'store_id'];
}
