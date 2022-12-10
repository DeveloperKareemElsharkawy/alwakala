<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingUser extends Model
{
    protected $fillable = ['language', 'push_notification', 'user_id'];
}
