<?php

namespace App\Http\Controllers\Seller;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Seller\Brands\BrandsResource;
use App\Http\Resources\Seller\Categories\CategoriesResource;
use App\Lib\Helpers\Categories\CategoryHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BrandsController extends BaseController
{
    public $storesRepo;
    private $lang;
    private $productsRepo;

    public function __construct(StoreRepository $storeRepository, Request $request, ProductRepository $productRepository)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->storesRepo = $storeRepository;
        $this->productsRepo = $productRepository;

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $brand = new Brand;
            $brand->name_ar = $request->name;
            $brand->name_en = $request->name;
            $brand->activation = 1;
            $brand->save();
            return response()->json([
                'status' => true,
                'message' => trans('messages.general.created'),
                'data' => $brand
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in store of seller brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getBrandsByCategory(Request $request)
    {
        try {
            if (!$request->category_id || !CategoryHelper::checkCategoryLevel('sub_sub_category', $request->category_id)) {
                return $this->error(['message' => trans('messages.category.un_valid_parent')]);
            }
            $query = Brand::query()->with('categoryBrand')
                ->where('activation', true)
                ->distinct('brands.id');

            if ($request->query('category_id')) {
                $query->whereHas('categoryBrand', function ($query) use ($request) {
                    $query->where('categories.id', $request->category_id);
                });
            }

            $brands = $query->OrderBy('brands.id', 'desc')->get();

            if (!count($brands))
                return $this->error(['message' => trans('messages.category.un_valid_parent')]);

            return $this->success(['message' => trans('messages.general.listed'), 'data' => BrandsResource::collection($brands)]);
        } catch (\Exception $e) {
            Log::error('error in getBrands of seller brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getAllBrandsForSellection()
    {
        try {
            $brands = Brand::query()
                ->where('activation', true)
                ->get();
            return $this->success(['message' => trans('messages.general.listed'), 'data' => BrandsResource::collection($brands)]);
        } catch (\Exception $e) {
            Log::error('error in getBrands of seller brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getBrandProducts($brandId, Request $request)
    {
        try {
            if (!Brand::query()->where('id', $brandId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.not_found'),
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

            $request->merge(['brand_id' => $brandId]);

            $products = $this->productsRepo->getProducts($arrayOfParameters);

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => $products
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getBrandProducts of seller brands' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function getSubCategoriesFromBrandProducts($brandId, Request $request)
    {
        try {
            if (!Brand::query()->where('id', $brandId)->first()) {
                return $this->error(['message' => trans('messages.general.not_found'),]);
            }

            $userId = UserId::UserId($request);

            $storeId = null;
            if ($userId) {
                $storeId = Store::query()
                    ->select('id')
                    ->where('user_id', $userId)
                    ->first()->id;
            }

            $categoriesIds = Product::query()->where([['brand_id', $brandId], ['reviewed', true]])->pluck('category_id')->toArray();

            $categories = Category::query()
                ->whereIn('id', $categoriesIds)
                ->where('activation', true)
                ->get();

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => CategoriesResource::collection($categories)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getBrandProducts of seller brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function getBrandStores($brandId, Request $request)
    {
        try {
            if (!Brand::query()->where('id', $brandId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.not_found'),
                    'data' => ''

                ], AResponseStatusCode::BAD_REQUEST);
            }
            $userId = UserId::UserId($request);
            $limit = 0;
            $pagination = 10;
            $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination, $brandId);
            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getBrandStores of seller brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
