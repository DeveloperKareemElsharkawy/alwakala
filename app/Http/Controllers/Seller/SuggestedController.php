<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Apps\AApps;
use App\Enums\Offers\AOfferTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\AppTv;
use App\Models\HomeSection;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SuggestedController extends BaseController
{
    public $productsRepo;
    public $storesRepo;

    public function __construct(ProductRepository $productRepository, StoreRepository $storeRepository)
    {
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
    }

    public function suggestedProducts(GetProductsRequest $request)
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
            $arrayOfParameters['pagination'] = 5;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);
            return response()->json([
                'status' => true,
                'message' => trans('messages.sections.just_for_you'),
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('error in suggestedProducts of seller Suggested' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function suggestedStores(Request $request)
    {
        try {
            $userId = UserId::UserId($request);
            $limit = 0;
            $pagination = 5;
            $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination);

            return response()->json([
                'status' => true,
                'message' => trans('messages.sections.category_store'),
                'data' => $stores

            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in suggestedStores of seller Suggested' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
