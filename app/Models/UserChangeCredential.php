<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserChangeCredential extends Model
{
    protected $fillable = [
        'user_id',
        'confirm_code',
        'credential_type',
        'credential'
    ];
}
