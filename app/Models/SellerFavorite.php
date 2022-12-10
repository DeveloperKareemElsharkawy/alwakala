<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerFavorite extends Model
{
    protected $fillable = [
        'favoriter_type',
        'favoriter_id',
        'favorited_type',
        'favorited_id',
    ];

    public function Favorited () {
        return $this->morphTo();
    }

    public function Favoriter () {
        return $this->morphTo();
    }
}
