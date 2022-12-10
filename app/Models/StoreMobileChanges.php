<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreMobileChanges extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'confirm_code', 'mobile'];
}
