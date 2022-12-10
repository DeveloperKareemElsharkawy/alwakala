<?php


namespace App\Http\Controllers\Seller;


use App\Http\Controllers\BaseController;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NonCompletedProductsController extends BaseController
{
    public function getNonCompletedProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            return ProductRepository::getNonCompletedProducts($request);
        } catch (\Exception $e) {
            Log::error('error in getNonCompletedProducts of seller deleteNonCompletedProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function deleteNonCompletedProducts($id, Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            return ProductRepository::deleteNonCompletedProducts($id, $request);
        } catch (\Exception $e) {
            Log::error('error in deleteNonCompletedProducts of seller  deleteNonCompletedProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }
}
