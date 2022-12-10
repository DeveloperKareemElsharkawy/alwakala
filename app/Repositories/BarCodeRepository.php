<?php


namespace App\Repositories;


use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\Controller;
use App\Models\BarcodeProduct;
use App\Models\ProductStore;
use App\Models\Store;


class BarCodeRepository extends Controller
{

    public static function createBarcodeProduct($request, $storeId)
    {
        if (self::checkBarcodeExistingForSeller($request, $storeId)) {
            return false;
        }
        BarcodeProduct::firstOrCreate([
            'barcode' => $request->barcode,
            'color_id' => $request->color_id,
            'product_id' => $request->product_id
        ]);
        return true;
    }

    private static function checkBarcodeExistingForSeller($request, $storeId)
    {
        $userProducts = ProductStore::query()
            ->select('product_id')
            ->where('store_id', $storeId)
            ->where('product_id', '!=', $request->product_id)
            ->pluck('product_id')->toArray();

        $barcode = BarcodeProduct::query()
            ->where('barcode', $request->barcode)
            ->where('color_id', $request->color_id)
            ->whereIn('product_id', $userProducts)
            ->first();
        if ($barcode) {
            return true;
        }
        return false;
    }

}
