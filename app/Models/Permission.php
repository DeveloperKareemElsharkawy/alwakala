<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'permission_name'
    ];

    Public function Roles() {
        return $this->belongsToMany(Role::class,'permission_roles', 'permission_id', 'role_id');
    }
}
