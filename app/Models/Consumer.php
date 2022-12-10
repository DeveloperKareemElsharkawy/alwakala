<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    protected $fillable = [
        'email',
        'password',
        'name',
        'image',
        'mobile',
    ];
    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ];

    public function SellerRate () {
        return $this->morphMany(SellerRate::class, 'rater');
    }
}
