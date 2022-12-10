<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Models\PackingUnitProduct;

class PackingUnitRepository extends Controller
{
    public static function packingUnitProduct($product_id)
    {
        return PackingUnitProduct::query()
            ->where('product_id', $product_id)
            ->where('packing_unit_id', 1)
            ->first();
    }
}
