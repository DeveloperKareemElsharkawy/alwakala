<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmailChanges extends Model
{
    use HasFactory;

    protected $fillable= ['email','user_id','confirm_code','has_changed'];

}
