<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Resources\Seller\Categories\CategoriesIndexResource;
use App\Http\Resources\Seller\Categories\CategoriesResource;
use App\Http\Resources\Seller\Categories\MainCategoriesResource;
use App\Http\Resources\Seller\Store\StoreMiniDataCollection;
use App\Http\Resources\Seller\Store\StoreMiniDataResource;
use App\Http\Resources\Seller\Store\StoreOpeningHoursResource;
use App\Http\Resources\Seller\StoreCollection;
use App\Http\Resources\Seller\StoreResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Category;
use App\Models\CategoryStore;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use App\Services\Categories\CategoriesService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends BaseController
{
    private $lang;
    public $productsRepo;
    public $storesRepo;
    public $categoriesService;

    public function __construct(Request $request, ProductRepository $productRepository, StoreRepository $storeRepository, CategoriesService $categoriesService)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
        $this->categoriesService = $categoriesService;
    }

    public function index()
    {
        try {
            $categories = Category::query()
                ->where('category_id', null)
                ->where('activation', true)
                ->with('brands')
                ->with('slides')
                ->with(array('childrenCategories' => function ($query) {
                    $query->select('id', 'name_' . $this->lang . ' as name', 'category_id', 'image');
                }))
                ->orderBy('id', 'asc')
                ->get();

            foreach ($categories as $category) {
                foreach ($category->childrenCategories as $child) {
                    $child->image = config('filesystems.aws_base_url') . $child->image;
                    foreach ($child->categories as $subChild) {
                        $subChild->image = config('filesystems.aws_base_url') . $subChild->image;
                    }
                }
            }
            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => MainCategoriesResource::collection($categories)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getForSelection(Request $request)
    {
        try {
            $store = Store::query()->where('user_id', $request->user_id)->first();
            $storeCategories = CategoryStore::query()->Where('store_id', $store->id)->pluck('category_id');
            $categories = Category::query()
                ->select('id', 'name_' . $this->lang . ' as name')
                ->whereIn('category_id', $storeCategories)
                ->where('activation', true)
                ->get();
            return response()->json([
                "status" => true,
                "message" => trans('messages.general.listed'),
                "data" => $categories
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getForSelection of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getCategoryProducts(GetProductsRequest $request)
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

    public function getCategoryStores(Request $request)
    {
        try {
            $userId = UserId::UserId($request);

//            if (is_null($request->query('category_id')) || !is_numeric($request->query('category_id'))) {
//                return response()->json([
//                    'status' => false,
//                    'message' => trans('messages.category.id_required'),
//                    'data' => ''
//                ], AResponseStatusCode::FORBIDDEN);
//            }

            $limit = 0;
            $pagination = 10;
            $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination);

            return new StoreCollection($stores);

        } catch (\Exception $e) {
            Log::error('error in getCategoryStores of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getCategories($type, Request $request)
    {
        try {
            $categories = '';
            if ($type == 'sub-sub') {
                $parentsId = Category::query()
                    ->where('category_id', null)
                    ->where('activation', true)
                    ->pluck('id')
                    ->toArray();
                $subChild = Category::query()
                    ->whereIn('category_id', $parentsId)
                    ->where('activation', true)
                    ->pluck('id')
                    ->toArray();
                $query = Category::query()
                    ->select('id', 'name_' . $this->lang . ' as name', 'category_id')
                    ->where('activation', true);
                if ($request->filled('id')) {
                    $query->where('category_id', $request->query('id'));
                } else {
                    $query->whereIn('category_id', $subChild);
                }
                $categories = $query->get();

            } elseif ($type == 'sub') {
                $parentsId = Category::query()
                    ->where('category_id', null)
                    ->where('activation', true)
                    ->pluck('id')
                    ->toArray();
                $query = Category::query()
                    ->select('id', 'name_' . $this->lang . ' as name', 'category_id')
                    ->where('activation', true);
                if ($request->filled('id')) {
                    $query->where('category_id', $request->query('id'));
                } else {
                    $query->whereIn('category_id', $parentsId);
                }
                $categories = $query->get();
            } elseif ($type == 'parent') {
                $categories = Category::query()
                    ->select('id', 'name_' . $this->lang . ' as name', 'category_id')
                    ->where('activation', true)
                    ->whereNull('category_id')
                    ->get();
            }

            $categories->load('categories');

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => CategoriesIndexResource::collection($categories)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCategories of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSubCategoryProducts(GetProductsRequest $request, $SubCategoryId)
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


            $subCategories_ids = Category::query()->where('category_id', $SubCategoryId)->pluck('id')->toArray();

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

    public function getSubCategoryProductsFilterData(GetProductsRequest $request, $SubCategoryId)
    {
        try {
            // Get Categories
             $subCategories_ids = Category::query()->where('category_id', $SubCategoryId)->pluck('id')->toArray();

            $products = Product::query()->with('sellers')->where('activation', true)->whereIn('category_id', $subCategories_ids)->get();

            $availableCategories = Category::query()->where('activation', true)->whereIn('id', $products->pluck('category_id')->toArray())->get();

            // Get Stores
            $storesIds = $products->pluck('sellers.*.store_id')->unique()->toArray();
            $storesListIds = array_merge(...$storesIds);

            $stores = Store::query()->whereIn('id', $storesListIds)->get();

            foreach ($stores as $store) {
                if ($store->logo)
                    $store->logo = config('filesystems.aws_base_url') . $store->logo;
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.created'),
                'data' => [
                    'categories' => CategoriesResource::collection($availableCategories),
                    'stores' => StoreMiniDataResource::collection($stores)
                ]
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getCategoryProducts of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSubCategoryStores(Request $request)
    {
        try {
            $userId = UserId::UserId($request);

            $limit = 0;
            $pagination = 10;
            $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination);

            return new StoreMiniDataCollection($stores);


        } catch (\Exception $e) {
            Log::error('error in getCategoryStores of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSubCategories($categoryId, Request $request)
    {
        try {
            $categories = Category::query()
                ->where([['activation', true], ['category_id', $categoryId]])
                ->get();

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => CategoriesResource::collection($categories)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCategoryStores of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
