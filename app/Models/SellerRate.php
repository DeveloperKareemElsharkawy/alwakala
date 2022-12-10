<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerRate extends Model
{
    protected $fillable = [
        'rater_type',
        'rater_id',
        'rated_type',
        'rated_id',
        'rate',
        'review',
        'rated_store_id',
        'image'
    ];


    public function Rated () {
        return $this->morphTo();
    }

    public function Rater () {
        return $this->morphTo();
    }


}
