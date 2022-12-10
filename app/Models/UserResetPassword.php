<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserResetPassword extends Model
{
    protected $fillable = [
        'user_id',
        'confirm_code'
    ];
}
