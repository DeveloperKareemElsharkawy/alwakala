<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Models\Bundle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class BundlesController extends BaseController
{
    public function getBundles()
    {
        try {

            $bundles = Bundle::query()->select('id', 'packing_unit_product_store_id', 'quantity', 'price')
                ->with(['packingUnitProductStore' => function ($query) {
                    $query->select('id', 'store_id', 'product_id')->
                    with(['products' => function ($productQuery) {
                        $productQuery->select('id', 'name_en');
                    }])
                        ->with(['stores' => function ($storeQuery) {
                            $storeQuery->select('id', 'name');
                        }])->
                        with(['packingUnitProductStoreAttributes' => function ($packingUnitProductStoreAttributesQuery) {
                            $packingUnitProductStoreAttributesQuery->
                            select('id', 'packing_unit_product_store_id')->
                            with(['packingUnitProductStoreAttributeImage' => function ($packingUnitProductStoreAttributeImage) {
                                $packingUnitProductStoreAttributeImage->select('id', 'packing_unit_product_store_attributes_id', 'image');
                            }]);
                        }]);
                }])
                ->get();

            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $bundles,
            ], AResponseStatusCode::SUCCESS);


        } catch (\Exception $e) {
            Log::error('error in getBundles of seller Bundles' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
