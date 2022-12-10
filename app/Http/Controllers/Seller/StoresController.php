<?php

namespace App\Http\Controllers\Seller;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Events\Store\FavoriteStore;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Resources\Seller\Branches\BranchResource;
use App\Http\Resources\Seller\ProductProfileResource;
use App\Jobs\Images\DeleteImageJob;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Http\Controllers\Controller;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Category;
use App\Models\CategoryStore;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerFavorite;
use App\Models\SellerRate;
use App\Models\Store;
use App\Models\StoreCall;
use App\Models\StoreImage;
use App\Models\User;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StoresController extends BaseController
{
    private $lang;
    public $productsRepo;

    public function __construct(Request $request, ProductRepository $productRepository)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->productsRepo = $productRepository;
    }

    public function rateStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|numeric|exists:stores,id',
                'rate' => 'required|numeric|min:1|max:5',
                'review' => 'nullable|max:255',
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $userId = UserId::UserId($request);

            $images = [];
            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $images[] = UploadImage::uploadImageToStorage($image, 'Feeds/store/rates');
                }
            }

            $sellerRate = SellerRate::query()->where([
                ['rater_type', User::class],
                ['rater_id', $userId],
                ['rated_type', Store::class],
                ['rated_id', $request->store_id],
            ])->first();

            if ($sellerRate) {
                $sellerRate->rate = $request->rate;
                $sellerRate->review = $request->review;
                if (count($images))
                    $sellerRate->images = json_encode($images);
                $sellerRate->save();
            } else {
                $sellerRate = new SellerRate();
                $sellerRate->rater_type = $request->rate;
                $sellerRate->rate = User::class;
                $sellerRate->rater_id = $userId;
                $sellerRate->rated_type = Store::class;
                $sellerRate->rated_id = $request->store_id;
                $sellerRate->rate = $request->rate;
                $sellerRate->review = $request->review;
                if (count($images))
                    $sellerRate->images = json_encode($images);
                $sellerRate->save();
            }


            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_rate_added'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in rateStore of seller Stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStoreProducts(GetProductsRequest $request)
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
                'message' => '',
                'data' => $products

            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getStoreProducts of seller Stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function toggleFavoriteStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|numeric|exists:stores,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $findFavoritedStore = SellerFavorite::query()
                ->where('favoriter_type', '=', User::class)
                ->where('favoriter_id', '=', $request->user_id)
                ->where('favorited_type', '=', Store::class)
                ->where('favorited_id', '=', $request->store_id)
                ->first();

            if (is_null($findFavoritedStore)) {

                $favorite = new SellerFavorite();
                $favorite->favoriter_type = User::class;
                $favorite->favoriter_id = $request->user_id;
                $favorite->favorited_type = Store::class;
                $favorite->favorited_id = $request->store_id;
                $favorite->save();
                $store = Store::query()->where('id', $request->store_id)->first();
                event(new FavoriteStore([$store->user_id], $request->store_id));
                return response()->json([
                    'success' => true,

                    'message' => trans('messages.stores.store_favorite'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);

            } else {

                $findFavoritedStore->delete();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.stores.store_unfavorite'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);

            }

        } catch (\Exception $e) {
            Log::error('error in toggleFavoriteStore of seller Stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function addStoreImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2024',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $storeId = Store::query()->where('user_id', $request->user_id)->first()->id;
            $imageCount = StoreImage::query()->where('store_id', $storeId)->count();

            if ($imageCount == 5) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.many_images'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            StoreImage::query()->create([
                'image' => UploadImage::uploadImageToStorage($request->image, 'stores/images'),
                'store_id' => $storeId,
            ]);


            return response()->json([
                'success' => true,
                'message' => trans('messages.stores.image_added'),
                'data' => '',
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in addStoreImage of seller Stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function deleteStoreImage(Request $request, $id)
    {
        try {

            if (StoreImage::query()->where('id', $id)->count() == 0) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.image_not_found'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $storeImage = StoreImage::query()->where('id', $request->id)->first();
            $job = (new DeleteImageJob($storeImage))->delay(Carbon::now()->addSeconds(30));
            dispatch($job);

            return response()->json([
                'success' => true,
                'message' => trans('messages.stores.image_deleted'),
                'data' => '',
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in deleteStoreImage of seller Stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeCall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|exists:stores,id',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $storeCall = new StoreCall;
            $storeCall->user_id = $request->user_id;
            $storeCall->store_id = $request->store_id;
            $storeCall->save();

            return response()->json([
                'success' => true,
                'message' => trans('messages.stores.call_added'),
                'data' => '',
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in storeCall of seller Stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function search(Request $request, $storeId)
    {
        try {
            $userId = UserId::UserId($request);
            $query = trim($request->input('query'));
            $PQuery = ProductRepository::prepareProductQuery($request, $userId, $storeId, $query, true);
            $products = $PQuery->paginate(10);
            foreach ($products as $product) {
                if ($product->discount != 0) {
                    $product->has_discount = true;
                    if ($product->discount_type == DiscountTypes::AMOUNT) {
                        $product->discount_type = 'amount';
                    } else {
                        $product->discount_type = 'percentage';
                        $product->discount = $product->discount . '%';
                    }
                } else {
                    $product->has_discount = false;
                }
                if (count($product->SellerRate) > 0) {
                    $product->rate = $product->SellerRate[0]->rate;
                } else {
                    $product->rate = 0;
                }
                unset($product->SellerRate);
                $product->image = null;
                if ($product->productImage) {
                    $product->image = config('filesystems.aws_base_url') . $product->productImage->image;
                }
                unset($product->productImage);
            }

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $products,
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in search of seller Stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeSubCategories(Request $request, $storeId)
    {
        try {
            if (!Store::query()->find($storeId)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.store_not_found'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $storeSubCategoriesIds = CategoryStore::query()->where('store_id', $storeId)->pluck('category_id');
            $subCategoriesIDs = Category::query()
                ->where('activation', true)
                ->whereIn('category_id', $storeSubCategoriesIds)
                ->pluck('id');

            $categories = Category::query()
                ->select('id', 'name_' . $this->lang . ' as name', 'image')
                ->where('activation', true)
                ->whereIn('category_id', $subCategoriesIDs)
                ->get();

            foreach ($categories as $category) {
                if ($category->image)
                    $category->image = config('filesystems.aws_base_url') . $category->image;
            }
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $categories,
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in storeSubCategories of seller Stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStoresForSelection(Request $request)
    {
        try {
            $user = auth('api')->user();
            $query = Store::query()
                ->leftJoin('users', 'users.id', '=', 'stores.user_id')
                ->where('users.activation', true)
                ->where('users.id', '!=', $user->id);
            if ($request->has('store_name') && $request->store_name != '') {
                $query->where('stores.name', 'ilike', '%' . $request->store_name . '%');
            }
            $stores = $query->select(['stores.id as id', 'stores.name as name'])->get();
            return response()->json([
                'message' => '',
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getStoresForSelection of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function storeSupplierBranches($storeId, Request $request)
    {
        try {
            $store = Store::query()->with('mainBranch.branches', 'branches')->find($storeId);

            if (!$store)
                return $this->error(['message' => trans('messages.stores.not_found')]);

            if ($store->is_main_branch) {
                $branchesForList = $store->branches->pluck('id')->toArray();
                $branchesForList[] = $store->id;
            } elseif ($store->mainBranch && !$store->is_main_branch) {
                $branchesForList = $store->mainBranch->branches->pluck('id')->toArray();
                $branchesForList[] = $store->mainBranch->id;
            }

            $stores = Store::query()->with('city.state.region.country', 'productsForFeedsV2')->whereIn('id', $branchesForList)
                ->where('store_type_id', StoreType::SUPPLIER)
                ->where('is_verified', true)->paginate(10);

            $orders = Order::query()->with('items')->where('user_id', 16)->get();

            return $this->respondWithPagination(BranchResource::collection($stores));

        } catch (\Exception $e) {
            Log::error('error in getStoresForSelection of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeRetailersBranches($storeId, Request $request)
    {
        try {
            $store = Store::query()->with('mainBranch.branches', 'branches')->find($storeId);

            if (!$store)
                return $this->error(['message' => trans('messages.stores.not_found')]);

            if ($store->is_main_branch) {
                $branchesForList = $store->branches->pluck('id')->toArray();
                $branchesForList[] = $store->id;
            } elseif ($store->mainBranch && !$store->is_main_branch) {
                $branchesForList = $store->mainBranch->branches->pluck('id')->toArray();
                $branchesForList[] = $store->mainBranch->id;
            }

            $stores = Store::query()->with('city.state.region.country', 'productsForFeedsV2')->whereIn('id', $branchesForList)
                ->where('store_type_id', StoreType::RETAILER)
                ->where('is_verified', true)->paginate(10);

            $orders = Order::query()->with('items')->where('user_id', 16)->get();

            return $this->respondWithPagination(BranchResource::collection($stores));

        } catch (\Exception $e) {
            Log::error('error in getStoresForSelection of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
