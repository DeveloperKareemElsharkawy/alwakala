<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingUnitProductAttribute extends Model
{
    protected $fillable = [
      'size_id',
      'quantity',
      'packing_unit_product_id'
    ];

    public function PackingUnitProduct(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PackingUnitProduct::class);
    }

    public function size(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
    

}
