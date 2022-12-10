<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Models\ProductStoreStock;


class ProductStoreStockRepository extends Controller
{

    public static function createProductStoreStock($productStore_id, $productAttr, $request, $stock)
    {
        ProductStoreStock::query()
            ->create([
                'product_store_id' => $productStore_id,
                'size_id' => $productAttr?$productAttr['size_id']:null,
                'color_id' => $request->color_id,
                'stock' => $stock,
                'reserved_stock' => 0,
                'available_stock' => 0,
                'sold' => 0,
                'returned' => 0,
            ]);
    }
}
