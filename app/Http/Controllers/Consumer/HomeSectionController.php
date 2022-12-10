<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\AChannels\AChannels;
use App\Enums\Apps\AApps;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Models\Store;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class HomeSectionController extends BaseController
{
    public $productsRepo;
    public $storesRepo;

    public function __construct(ProductRepository $productRepository, StoreRepository $storeRepository)
    {
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
    }

    public function hotOffers(GetProductsRequest $request)
    {
        try {
            $userId = UserId::UserId($request);
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = null;
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            $products = $this->productsRepo->getProducts($arrayOfParameters);


            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $products
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in get hotOffers of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function categoryProduct(GetProductsRequest $request)
    {
        try {
            $userId = UserId::UserId($request);
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = null;
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            $products = $this->productsRepo->getProducts($arrayOfParameters);

            $response = [
                //TODO check selected product
                'selected_product' => $this->getSelectedProduct($request, $userId),
                'products' => $products
            ];
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $response
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in categoryProducts of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function brands(Request $request)
    {
        try {
            $userId = UserId::UserId($request);
            $limit = 0;
            $pagination = 10;
            $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination);
            return response()->json([
                'status' => true,
                'message' => 'brands',
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in brands of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    // Global Collections
    public function newArrivals(GetProductsRequest $request)
    {
        try {
            $userId = UserId::UserId($request);
            $request['newArrivals'] = true;
            $arrayOfParameters['pagination'] = (isset($_GET['pageSize'])) ? $_GET['pageSize'] : 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            $arrayOfParameters['storeId'] = (isset($_GET['store_id'])) ? $_GET['store_id'] : null;
            if (isset($_GET['store_id']))
                $arrayOfParameters['isStoreProfile'] = true;
            $products = $this->productsRepo->getProducts($arrayOfParameters);


            return response()->json([
                'status' => true,
                'message' => 'new arrival',
                'data' => $products
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in newArrivals of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function mostPopular(GetProductsRequest $request)
    {
        try {
            $userId = UserId::UserId($request);
            $arrayOfParameters['mostPopular'] = true;
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = null;
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            $products = $this->productsRepo->getProducts($arrayOfParameters);
            $productsArray = $products->sortByDesc('number_of_views')->toArray();
            return response()->json([
                'status' => true,
                'message' => 'most popular',
                'data' => array_values($productsArray)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in mostPopular of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storesForYou(Request $request)
    {
        try {
            $userId = UserId::UserId($request);
            $limit = 0;
            $pagination = 10;
            $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination);

            return response()->json([
                'status' => true,
                'message' => 'brands',
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in storesForYou of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function feeds(Request $request)
    {
        try {
            /* $userId = UserId::UserId($request);
             $limit = 0;
             $pagination = 10;
             $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination);*/

            return response()->json([
                'status' => true,
                'message' => 'feeds list',
                'data' => ProductRepository::feeds($request->store_id, $request->page)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in feeds of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function getSelectedProduct(GetProductsRequest $request, $userId)
    {
        try {
            $storeId = null;
            if ($userId) {
                $storeId = Store::query()
                    ->where('user_id', $userId)
                    ->first();
            }
            $arrayOfParameters['pagination'] = 0;
            $arrayOfParameters['limit'] = 1;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            return $this->productsRepo->getProducts($arrayOfParameters);
        } catch (\Exception $e) {
            Log::error('error in getSelectedProduct of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }


    }

    public function sales(GetProductsRequest $request)
    {
        try {
            $request['discount'] = true;
            $request['channel'] = AChannels::CONSUMER_APP;
            $arrayOfParameters['pagination'] = (isset($_GET['pageSize'])) ? $_GET['pageSize'] : 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = UserId::UserId($request);
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            $arrayOfParameters['storeId'] = (isset($_GET['store_id'])) ? $_GET['store_id'] : null;
            if (isset($_GET['store_id'])) {
                $arrayOfParameters['isStoreProfile'] = true;
            }
            return response()->json([
                'status' => true,
                'message' => 'Sales',
                'data' => $this->productsRepo->getProducts($arrayOfParameters)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in discount of seller  home' . __LINE__ . $e);
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
            $arrayOfParameters['pagination'] = (isset($_GET['pageSize'])) ? $_GET['pageSize'] : 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = true;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
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

    public function exploreProduct(Request $request)
    {
        try {
            $request['channel'] = AChannels::CONSUMER_APP;
            $arrayOfParameters['pagination'] = (isset($_GET['pageSize'])) ? $_GET['pageSize'] : 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = true;
            if($request->category_id){
                $arrayOfParameters['isStoreProfile'] = false;
            }
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = UserId::UserId($request);
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            $arrayOfParameters['storeId'] = (isset($request->store_id)) ? $request->store_id : null;
            return response()->json([
                'status' => true,
                'message' => 'Products',
                'data' => $this->productsRepo->getProducts($arrayOfParameters)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in explore products in home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function justForYou(Request $request)
    {
        try {
            $request['just_for_you']=true;
            // $request['type_id']=AApps::SELLER_APP;
            $arrayOfParameters['pagination'] = (isset($_GET['pageSize'])) ? $_GET['pageSize'] : 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = UserId::UserId($request);
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            $arrayOfParameters['storeId'] = null;

            return response()->json([
                'status' => true,
                'message' => 'Products',
                'data' => $this->productsRepo->getProducts($arrayOfParameters)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in just for you in home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
