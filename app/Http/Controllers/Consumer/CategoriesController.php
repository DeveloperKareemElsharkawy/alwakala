<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\Apps\AApps;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Categories\CategoryHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\S3\S3StorageHandler;
use App\Models\AppTv;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Store;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use App\Services\Categories\CategoriesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriesController extends BaseController
{
    private $lang;
    public $productsRepo, $storesRepo, $categoriesService, $s3StorageHandler;

    public function __construct(Request           $request,
                                ProductRepository $productRepository,
                                CategoriesService $categoriesService,
                                StoreRepository   $storeRepository,
                                S3StorageHandler $s3StorageHandler)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
        $this->categoriesService = $categoriesService;
        $this->s3StorageHandler = $s3StorageHandler;
    }

    public function index()
    {
        try {
            $categories = Category::query()
                ->select('id', 'name_' . $this->lang . ' as name', 'category_id', 'image')
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
                $category->image = $this->s3StorageHandler->generateS3ImagePreSignedUrl($category->image, 2);
                foreach ($category->brands as $brand) {
                    $brand->image = config('filesystems.aws_base_url') . $brand->image;
                }
                foreach ($category->slides as $slide) {
                    $slide->image = config('filesystems.aws_base_url') . $slide->image;
                }
                foreach ($category->childrenCategories as $child) {
                    $child->image = config('filesystems.aws_base_url') . $child->image;
                    foreach ($child->categories as $subChild) {
                        $subChild->image = config('filesystems.aws_base_url') . $subChild->image;
                    }
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Categories',
                'data' => $categories
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getParentCategories(Request $request)
    {
        try {
            $mainCategories = $this->categoriesService->getCategoriesByLevel(null, $this->lang, 'parent');
            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => $mainCategories
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in list main Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSelectedCategoryData(Request $request, $categoryId)
    {
        try {
            if (!CategoryHelper::checkCategoryLevel('parent', $categoryId)) {
                return $this->notFound();
            }

            $categories = Category::query()
                ->select('id', 'name_' . $this->lang . ' as name', 'category_id', 'image')
                ->where('category_id', $categoryId)
                ->where('activation', true)
                ->with(array('categories' => function ($query) {
                    $query->select('id', 'name_' . $this->lang . ' as name', 'category_id', 'image')
                        ->where('activation', true);
                }))
                ->get();

            foreach ($categories as $category) {
                foreach ($category->categories as $c) {
                    $c->image = $c->image ? config('filesystems.aws_base_url') . $c->image : null;
                }
            }

            $slides = AppTv::query()
                ->select('id', 'image', 'item_id', 'item_type')
                ->where('category_id', $categoryId)
                ->whereDate('expiry_date', '>=', Carbon::today())
                ->where('app_id', AApps::CONSUMER_APP)
                ->get();


            foreach ($slides as $slide) {
                $slide->image = $slide->image ? config('filesystems.aws_base_url') . $slide->image : null;
            }
            $brands = Brand::query()
                ->select('id', 'name_' . $this->lang . ' as name', 'image')
                ->where('activation', true)
                ->get();
            foreach ($brands as $brand) {
                $brand->image = $brand->image ? config('filesystems.aws_base_url') . $brand->image : null;
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => [
                    'categories' => $categories,
                    'slides' => $slides,
                    'brands' => $brands,

                ]
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in list main Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getCategoryProducts(Request $request)
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
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            $products = $this->productsRepo->getProducts($arrayOfParameters);


            return response()->json([
                'status' => true,
                'message' => 'category products',
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

            if (is_null($request->query('category_id')) || !is_numeric($request->query('category_id'))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category Id Is Required',
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            $limit = 0;
            $pagination = 10;
            $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination);

            return response()->json([
                'status' => true,
                'message' => 'category store',
                'data' => $stores

            ], AResponseStatusCode::SUCCESS);

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
                    ->select('id', 'name_' . $this->lang . ' as name')
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
                    ->select('id', 'name_' . $this->lang . ' as name')
                    ->where('activation', true);
                if ($request->filled('id')) {
                    $query->where('category_id', $request->query('id'));
                } else {
                    $query->whereIn('category_id', $parentsId);
                }
                $categories = $query->get();
            } elseif ($type == 'parent') {
                $categories = Category::query()
                    ->select('id', 'name_' . $this->lang . ' as name')
                    ->where('activation', true)
                    ->whereNull('category_id')
                    ->get();
            }
            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => $categories
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCategories of seller Categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
