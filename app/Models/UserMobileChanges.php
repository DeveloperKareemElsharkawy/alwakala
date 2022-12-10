<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMobileChanges extends Model
{
    use HasFactory;

    protected $fillable= ['mobile','user_id','confirm_code'];
}
