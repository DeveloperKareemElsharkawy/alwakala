<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingUnitProduct extends Model
{
    protected $fillable = [
        'product_id',
        'packing_unit_id',
        'basic_unit_count',
        'basic_unit_id',
    ];
    protected $table = 'packing_unit_product';

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function attributes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PackingUnitProductAttribute::class, 'packing_unit_product_id', 'id');
    }

}
