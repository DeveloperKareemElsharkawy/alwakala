<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Store\FollowStore;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Consumer\Store\StoreIndexResource;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\FollowedStore;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Repositories\StoreRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class FollowedStoreController extends BaseController
{
    public $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param Request $request
     * @param $store_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function followedStoresToggle(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => [
                    'required',
                    'numeric',
                    'exists:stores,id',
                ],
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $store_id = $request->store_id;

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
                $store = Store::query()->where('id', $store_id)->first();
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

    public function getFollowedStoresV2(Request $request)
    {
        try {
            $userId = $request->user_id;

            $q = Store::query()
                ->select('stores.id', 'stores.name', 'stores.logo',
                    DB::raw('CASE WHEN COUNT(followed_stores.store_id) > 0 THEN true else false END as is_followed'))
                ->leftJoin('category_store', 'category_store.store_id', '=', 'stores.id')
                ->join('followed_stores', function ($join) use ($userId) {
                    $join->on('stores.id', 'followed_stores.store_id')
                        ->where('followed_stores.user_id', $userId);
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
           return $this->respondWithPagination($stores);
        } catch (\Exception $e) {
            Log::error('error in getFollowedStores of seller  FollowedStore' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function getFollowedStores(Request $request)
    {
        try {
            $followedStoresIds = FollowedStore::where('user_id',$request->user_id)->get()->pluck('store_id')->toArray();

            $stores = Store::query()->with(['city.state.region.country', 'storeSettings', 'storeOpeningHours', 'owner'])
                ->with(array('SellerRate' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                    $query->take(3);
                }))
                ->whereIn('id', $followedStoresIds)
                ->withCount('products')->paginate(10);

            return $this->respondWithPagination(StoreIndexResource::collection($stores));
        } catch (\Exception $e) {
            Log::error('error in getFollowedStores of seller  FollowedStore' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


}
