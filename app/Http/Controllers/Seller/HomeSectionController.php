<?php

namespace App\Http\Controllers\Seller;

use App\Enums\AChannels\AChannels;
use App\Enums\Apps\AApps;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Resources\Seller\Categories\CategoriesResource;
use App\Http\Resources\Seller\Feeds\FeedsCollection;
use App\Http\Resources\Seller\Slider\SlidesResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\Store;
use App\Repositories\FeedsRepository;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HomeSectionController extends BaseController
{
    public $productsRepo;
    public $storesRepo;
    public $lang;

    public function __construct(ProductRepository $productRepository, StoreRepository $storeRepository, Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
    }

    public function getSections(): \Illuminate\Http\JsonResponse
    {
        try {
            $sections = HomeSection::query()
                ->where('activation', true)
                ->get();
            foreach ($sections as $section) {
                if ($section->image)
                    $section->image = config('filesystems.aws_base_url') . $section->image;
            }
            return response()->json([
                'status' => true,
                'message' => trans('messages.sections.sections'),
                'data' => $sections
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in get sections of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function hotOffers(GetProductsRequest $request)
    {
        try {
            $categoriesIds = Product::query()->whereHas('productStore', function ($query) {
                $query->where('discount', '!=', null);
            })->pluck('category_id')->toArray();

            $categories = Category::query()
                ->whereIn('id', $categoriesIds)
                ->where('activation', true)
                ->get();

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

            return $this->respondWithPagination($products);

        } catch (\Exception $e) {
            Log::error('error in get hotOffers of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function hotOffersCategories(GetProductsRequest $request)
    {
        try {
            $categoriesIds = Product::query()->whereHas('productStore', function ($query) {
                $query->where('discount', '!=', null);
            })->pluck('category_id')->toArray();

            $categories = Category::query()
                ->whereIn('id', $categoriesIds)
                ->where('activation', true)
                ->get();

            return response()->json([
                'status' => true,
                'message' => '',
                'data' => CategoriesResource::collection($categories)
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

    public function brands(GetProductsRequest $request)
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

            $categories = Category::query()->where('activation', true);

            if ($request->brand_id) {
                $categories->whereHas('products', function ($q) use ($storeId) {
                    $q->where('products.brand_id', '=', request()->brand_id);
                    $q->whereHas('productStore', function ($q) use ($storeId) {
                        $q->where('store_id', '!=', $storeId);
                    });
                });
            }

            if ($storeId) {
                $categories->whereHas('products.productStore', function ($q) use ($storeId) {
                    $q->where('store_id', '!=', $storeId);
                });
            }


            $brands = Brand::query()
                ->select('id', "name_$this->lang as name", 'image')
                ->where('activation', true)
                ->limit(5)->get();
            foreach ($brands as $brand) {
                if ($brand->image)
                    $brand->image = config('filesystems.aws_base_url') . $brand->image;
            }


            $data = [
                'brands' => $brands,
                'categories' => CategoriesResource::collection($categories->get()),
                'products' => $products->items()
            ];

            return $this->respondPaginationWithAdditionalData($products, $data);


        } catch (\Exception $e) {
            return $e;
            Log::error('error in brands of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    // Global Collections
    public function newArrivals(GetProductsRequest $request)
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
            $request['newArrivals'] = true;
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);


            return response()->json([
                'status' => true,
                'message' => trans('messages.sections.new_arrivals'),
                'data' => $products
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in newArrivals of seller  home sections' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function mostPopular(GetProductsRequest $request)
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
            $arrayOfParameters['mostPopular'] = true;
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);

            $this->respondWithPagination($products);
            return response()->json([
                'status' => true,
                'message' => trans('messages.sections.most_popular'),
                'data' => $products
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
                'message' => trans('messages.sections.brands'),
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return $e;
            Log::error('error in storesForYou of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function feeds(Request $request)
    {
        try {
            $feedsRepository = new FeedsRepository();
            return response()->json([
                'status' => true,
                'message' => 'feeds',
                'data' => $feedsRepository->showAllFeeds($request)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            dd($e);
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
            return $this->productsRepo->getProducts($arrayOfParameters);
        } catch (\Exception $e) {
            Log::error('error in getSelectedProduct of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function discount(GetProductsRequest $request)
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
            $request['discount'] = true;
            $arrayOfParameters['pagination'] = $request->pageSize;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            return $this->productsRepo->getProducts($arrayOfParameters);
        } catch (\Exception $e) {
            Log::error('error in discount of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSection($sectionId)
    {
        try {
            $homeSection = HomeSection::query()->with('slides.type')->whereHas('slides', function ($q) {
                $q->where('app_id', 1);
            })->find($sectionId);

            return SlidesResource::collection($homeSection['slides']);


        } catch (\Exception $e) {
            Log::error('error in getSelectedProduct of seller  home sections' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function justForYou(Request $request)
    {
        try {
            $request['just_for_you'] = true;
            // $request['type_id']=AApps::SELLER_APP;
            $arrayOfParameters['pagination'] = (isset($_GET['pageSize'])) ? $_GET['pageSize'] : 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = UserId::UserId($request);
            $arrayOfParameters['app'] = AApps::SELLER_APP;
            $arrayOfParameters['storeId'] = (isset($request->store_id)) ? $request->store_id : null;

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

