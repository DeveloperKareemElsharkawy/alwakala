<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Resources\Seller\Feeds\FeedsCollection;
use App\Http\Resources\Seller\Slider\SlidesResource;
use App\Lib\Helpers\UserId\UserId;
use App\Models\AppTv;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Store;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SlidesController extends BaseController
{
    public $productsRepo;
    public $storesRepo;

    public function __construct(ProductRepository $productRepository, StoreRepository $storeRepository)
    {
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
    }

    public function products(GetProductsRequest $request, $appTv)
    {
        try {
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

            $slide = AppTv::query()->find($appTv);

            if(!$slide){
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $request->merge(['products_ids' => json_decode($slide->items_ids)]);

            $products = $this->productsRepo->getProducts($arrayOfParameters);

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.created'),
                'data' => $products
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getCategoryProducts of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function category_products(GetProductsRequest $request, $appTv)
    {
        try {
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

            $slide = AppTv::query()->find($appTv);

            if(!$slide){
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $subCategories_ids = Category::query()->where('category_id', $slide['item_id'])->pluck('id')->toArray();

            $subCategories_ids[] = $slide['item_id'];

            $request->merge(['categories_ids' => $subCategories_ids]);

            $products = $this->productsRepo->getProducts($arrayOfParameters);

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.created'),
                'data' => $products
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getCategoryProducts of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}

