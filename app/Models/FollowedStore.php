<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowedStore extends Model
{
    protected $fillable = ['user_id', 'store_id'];
    public function store()
    {
        return $this->belongsToMany(Store::class, 'store_id','id');
    }
    public function User()
    {
        return $this->belongsToMany(User::class, 'user_id','id');
    }
}
