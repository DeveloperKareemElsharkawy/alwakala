<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferProduct extends Model
{
    use SoftDeletes;

    protected $table = 'offer_product';

    protected $fillable = ['offer_id', 'product_id', 'deleted_at'];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
