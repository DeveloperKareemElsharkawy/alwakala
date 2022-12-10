<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Models\ProductStore;


class ProductStoreRepository extends Controller
{
    public function getProductStore($product_id, $store_id)
    {
        return ProductStore::query()
            ->where('product_id', $product_id)
            ->where('store_id', $store_id)
            ->first();
    }

}
