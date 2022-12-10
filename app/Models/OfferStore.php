<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferStore extends Model
{
    protected $table = 'offer_store';

    protected $fillable = ['offer_id', 'store_id', 'status'];

    public function offer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }


    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }


}
