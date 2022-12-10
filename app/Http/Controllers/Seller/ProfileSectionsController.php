<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Http\Controllers\Controller;
use App\Lib\Log\ValidationError;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileSectionsController extends BaseController
{
    public $productsRepo;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productsRepo = $productRepository;
    }

    public function hotOffer(GetProductsRequest $request, $storeId)
    {
        try {

            if (!Store::query()->where('id', $storeId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.store_not_found'),
                    'data' => ''

                ], AResponseStatusCode::BAD_REQUEST);
            }
            $userId = UserId::UserId($request);
            $storeId = null;
            if ($userId) {
                $storeId = Store::query()
                    ->select('id')
                    ->where('user_id', $userId)
                    ->first()->id;
            }
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);

            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_hot_offers'),
                "data" => $products
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in hotOffer of seller ProfileSections ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function bestSelling(GetProductsRequest $request, $storeId)
    {
        try {

            if (!Store::query()->where('id', $storeId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.store_not_found'),
                    'data' => ''

                ], AResponseStatusCode::BAD_REQUEST);
            }
            $userId = UserId::UserId($request);
            $arrayOfParameters['pagination'] = 0;
            $arrayOfParameters['limit'] = 3;
            $arrayOfParameters['isStoreProfile'] = true;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);


            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_best_selling'),
                "data" => $products
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in bestSelling of seller ProfileSections ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function newArrival(GetProductsRequest $request, $storeId)
    {
        try {

            if (!Store::query()->where('id', $storeId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.store_not_found'),
                    'data' => ''

                ], AResponseStatusCode::BAD_REQUEST);
            }
            $userId = UserId::UserId($request);
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = true;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);


            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_new_arrival'),
                "data" => $products
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in newArrival of seller ProfileSections ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function categoryProduct(GetProductsRequest $request, $storeId)
    {
        try {

            if (!Store::query()->where('id', $storeId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.store_not_found'),
                    'data' => ''

                ], AResponseStatusCode::BAD_REQUEST);
            }

            $userId = UserId::UserId($request);
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = true;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);


            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_category_products'),
                "data" => $products
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in categoryProduct of seller ProfileSections ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
