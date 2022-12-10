<?php

namespace App\Repositories;

use App\Models\PackingUnitProduct;

class PackingUnitProductRepository
{

    public function getProductPackingUnit(int $packingUnitId,int $productId) {
       return PackingUnitProduct::query()
            ->where('packing_unit_id', $packingUnitId)
            ->where('product_id', $productId)
            ->first();
    }
}
