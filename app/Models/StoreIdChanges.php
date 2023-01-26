<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreIdChanges extends Model
{
    use HasFactory;

    protected $fillable = ['old_store_profile_id','store_id'];
}
