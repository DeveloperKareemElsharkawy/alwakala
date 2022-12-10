<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Events\Store\FollowStore;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\FollowedStore;
use App\Models\ProductStore;
use App\Models\Store;
use App\Models\User;
use App\Models\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StoreRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class FollowedStoreController extends BaseController
{
    public $storeRepository;
    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function followedStoresToggle(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|numeric|exists:stores,id'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $store_id = $request->store_id;
            $store = Store::query()->where('id', $store_id)->first();
            if ($store->user_id == $request->user_id) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.follow_denied'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            if (!$this->storeRepository->ifAllowTofollow($request)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.follow_denied'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $followed_store = StoreRepository::findFollowedStoreByUser($request['user_id'], $store_id);
            if ($followed_store) {
                StoreRepository::deleteFollowedStoreByUser($request['user_id'], $store_id);
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.stores.un_follow_store'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            } else {
                StoreRepository::addFollowedStoreByUser($request['user_id'], $store_id);

                event(new FollowStore([$store->user_id], $store_id));
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.stores.follow_store'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }
        } catch (\Exception $e) {
            Log::error('error in followedStoresToggle of seller  FollowedStore' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getFollowedStores(Request $request)
    {
        try {
            $pageSize = $request->pageSize ? $request->pageSize : 10;
            $userId = UserId::UserId($request);
            $storeWithProducts = FollowedStore::query()
                ->where('user_id', $userId)
                ->pluck('store_id')
                ->toArray();
            $q = Store::query()->whereHas('products', function ($q) {
                $q->where('reviewed', true);
            })
                ->select('stores.id', 'stores.name', 'stores.logo','stores.description','stores.store_type_id',
                    DB::raw('CASE WHEN COUNT(followed_stores.store_id) > 0 THEN true else false END as is_followed'))
                ->leftJoin('category_store', 'category_store.store_id', '=', 'stores.id')
                ->leftJoin('followed_stores', function ($join) use ($userId) {
                    $join->on('stores.id', 'followed_stores.store_id')
                        ->where('followed_stores.user_id', $userId);
                })
                ->with('SellerRate')
//                ->where('stores.user_id', '!=', $userId)
                ->where('stores.store_type_id', StoreType::SUPPLIER)
                ->whereIn('stores.id', $storeWithProducts);


            if ($request->filled('brand_id')) {
                $brandId = $request->brand_id;
                $q->whereHas('brands', function ($qu) use ($brandId) {
                    $qu->where('brand_id', $brandId);
                });
            }
            if ($request->query('category_id')) {
                $q->where('category_store.category_id', $request->query('category_id'));
            }
            if ($request->filled('city_id')) {
                $q->where('stores.city_id', $request->query('city_id'));
            }

            $stores = $q->groupBy(['stores.id'])
                ->paginate($pageSize);

            $isActive = false;
            if ($userId) {
                $isActive = User::query()
                    ->select('activation')
                    ->where('id', $userId)
                    ->first()->activation;
            }
            foreach ($stores as $store) {
                $store['number_of_followers'] = FollowedStore::query()->select('id')
                    ->where('store_id', $store->id)->get()->count();
                $store['number_of_views'] = View::query()->select('id')
                    ->where('item_type', '=', 'STORE')
                    ->where('item_id', $store->id)->get()->count();
                if (count($store->SellerRate) > 0) {
                    $store->rate = $store->SellerRate[0]->rate;
                } else {
                    $store->rate = 0;
                }
                unset($store->SellerRate);

                foreach ($store->products as $product) {
                    if ($product->productImage) {
                        $product->image = $product->productImage->image;
                    } else {
                        $product->image = null;
                    }
                    $product->price = ProductHelper::canShowPrice($userId, $isActive, $product->price);
                    $product->net_price = ProductHelper::canShowPrice($userId, $isActive, $product->net_price);
                    unset($product->productImage);
                    unset($product->pivot);
                    if ($product->image) {
                        $product->image = config('filesystems.aws_base_url') . $product->image;
                    }
                }
                if ($store->logo) {
                    $store->logo = config('filesystems.aws_base_url') . $store->logo;
                }
            }
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $stores

            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getFollowedStores of seller  FollowedStore' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getFollowersStores(Request $request)
    {
        try {
            $userId = UserId::UserId($request);
            $store = Store::query()->where('user_id', $userId)->first();
            $q = Store::query()
                ->whereHas('products')
                ->select('stores.id', 'stores.name', 'stores.logo',
                    DB::raw('CASE WHEN COUNT(followed_stores.store_id) > 0 THEN true else false END as is_followed'))
                ->leftJoin('category_store', 'category_store.store_id', '=', 'stores.id')
                ->join('followed_stores', function ($join) use ($store) {
                    $join->on('stores.user_id', 'followed_stores.user_id')
                        ->where('followed_stores.store_id', $store->id);
                })
                ->with(['products' => function ($query) {
                    $query->where('reviewed', true);
                }])
                ->with('SellerRate');

            if ($request->filled('category_id')) {
                $q->where('category_store.category_id', $request->query('category_id'));
            }
            if ($request->filled('city_id')) {
                $q->where('stores.city_id', $request->query('city_id'));
            }
            $q->groupBy('stores.id');
            $stores = $q->paginate(10);

            foreach ($stores as $store) {
                if (count($store->SellerRate) > 0) {
                    $store->rate = $store->SellerRate[0]->rate;
                } else {
                    $store->rate = 0;
                }
                unset($store->SellerRate);

                foreach ($store->products as $product) {
                    if ($product->productImage) {
                        $product->image = $product->productImage->image;
                    } else {
                        $product->image = null;
                    }
                    unset($product->productImage);
                    unset($product->pivot);
                    if ($product->image)
                        $product->image = config('filesystems.aws_base_url') . $product->image;
                }
                if ($store->logo)
                    $store->logo = config('filesystems.aws_base_url') . $store->logo;
            }
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $stores

            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getFollowedStores of seller  FollowedStore' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getFollowedStoresCount(Request $request)
    {
        try {
            $userId = UserId::UserId($request);
            $storesCount = $this->storeRepository->getFollowedStoresCount($userId);
            return $this->success([
                'message' => trans('messages.stores.stores_count'),
                'data' => $storesCount
            ]);
        } catch (\Exception $e) {
            Log::error('error in getFollowedStores of seller  FollowedStore' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
