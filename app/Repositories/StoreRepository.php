<?php


namespace App\Repositories;


use App\Enums\Orders\AOrders;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Http\Controllers\Controller;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\FollowedStore;
use App\Models\OrderProduct;
use App\Models\ProductStore;
use App\Models\SellerRate;
use App\Models\Store;
use App\Models\StoreRate;
use App\Models\User;
use App\Models\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StoreRepository extends Controller
{

    protected $model;

    public function __construct(Store $model)
    {
        $this->model = $model;
    }

    public function getStores($request, $userId, $limit, $pagination, $brandId = null, $storeId = null, $isSupplierType = true, $hasProducts = true)
    {
        $storeWithProducts = ProductStore::query()->select('store_id')->distinct()->pluck('store_id')->toArray();

        $latitude = $request->header('Latitude');
        $longitude = $request->header('Longitude');

        $q = Store::query()
            ->select('stores.id', 'stores.store_type_id', 'stores.name', 'stores.logo',
                DB::raw('CASE WHEN COUNT(followed_stores.store_id) > 0 THEN true else false END as is_followed'),
            )
            ->leftJoin('category_store', 'category_store.store_id', '=', 'stores.id')
            ->leftJoin('followed_stores', function ($join) use ($userId) {
                $join->on('stores.id', 'followed_stores.store_id')
                    ->where('followed_stores.user_id', $userId);
            })
            ->with('SellerRate')
            ->when($isSupplierType, function ($query) {
                $query->where('stores.store_type_id', StoreType::SUPPLIER);
            })
            ->when($hasProducts, function ($query) {
                $query->whereHas('products', function ($q) {
                    $q->where('reviewed', true);
                });
            })
            ->where('stores.user_id', '!=', $userId);

        if ($storeId) {
            $q->where('stores.id', $storeId);
        }

        $latitude = $request->header('Latitude');
        $longitude = $request->header('Longitude');

        if ($request->header('latitude') && $request->header('longitude')) {
            // This will calculate the distance in km
            // if you want in miles use 3959 instead of 6371

            $q->selectRaw(DB::raw('6371 * acos(cos(radians(' . $latitude . ')) * cos(radians(stores.latitude)) * cos(radians(stores.longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(stores.latitude))) as distance'))
                ->orderByRaw('distance asc');
        }


        if ($brandId) {
            $q->whereHas('brands', function ($qu) use ($brandId) {
                $qu->where('brand_id', $brandId);
            });
        }
        if ($request->query('category_id')) {
            $q->where('category_store.category_id', $request->query('category_id'));
        }
        if ($request['sub_category_id']) {
            $q->where('category_store.category_id', $request['sub_category_id']);
        }
        if ($request->filled('city_id')) {
            $q->where('stores.city_id', $request->query('city_id'));
        }

        if ($request->filled('name')) {
            $q->where('name', 'LIKE', "%{$request->query('name')}%") ;
         }

        if ($request['where_stores_ids']) {
            $q->whereIn('stores.id', $request['where_stores_ids']);
        }

        $q->groupBy([
            'stores.id',
        ]);
        if ($limit != 0) {
            $stores = $q->limit($limit)->get();
        } elseif ($pagination != 0) {
            $stores = $q->paginate($pagination);
        } else {
            $stores = $q->get();
        }

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
//                unset($product->productPrice);
                unset($product->productImage);
                unset($product->pivot);
                if ($product->image)
                    $product->image = config('filesystems.aws_base_url') . $product->image;
            }
            if ($store->logo)
                $store->logo = config('filesystems.aws_base_url') . $store->logo;
        }
        return $stores;
    }

    /**
     * @param $userId
     * @param $storeId
     * @return bool
     */
    public static function addFollowedStoreByUser($userId, $storeId): bool
    {

        try {
            $followed_store = new FollowedStore();
            $followed_store->user_id = $userId;
            $followed_store->store_id = $storeId;
            $followed_store->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteFollowedStoreByUser($userId, $storeId): bool
    {
        try {
            FollowedStore::query()
                ->where(['store_id' => $storeId, 'user_id' => $userId])
                ->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    public static function findFollowedStoreByUser($userId, $storeId)
    {
        return FollowedStore::query()
            ->where(['store_id' => $storeId, 'user_id' => $userId])
            ->first();

    }

    public function getStore($store_id)
    {
        return Store::query()->where(['id' => $store_id])->first();
    }

    public function ifAllowTofollow($request)
    {
        $store = $this->getStore($request->store_id);
        if ($request['type_id'] == UserType::SELLER && $store->store_type_id == StoreType::RETAILER) {
            return false;
        } elseif ($request['type_id'] == UserType::CONSUMER && $store->store_type_id == StoreType::SUPPLIER) {
            return false;
        }

        return true;
    }

    public static function getStoreByUserId($user_id, $where = [])
    {
        $q = Store::query()->where('user_id', $user_id);
        if (count($where) > 0) {
            $q->where($where);
        }
        return $q->first();
    }

    public static function getStoreByStoreId($storeId, $where = [])
    {
        $q = Store::query()->where('id', $storeId);
        if (count($where) > 0) {
            $q->where($where);
        }
        return $q->first();
    }

    public function getSupplierProductsByUserId($userId, $productIds)
    {
        $query = Store::query()
            ->join('product_store', 'product_store.store_id', '=', 'stores.id')
            ->where('stores.user_id', $userId);
        if (count($productIds)) {
            $query->whereIn('product_store.product_id', $productIds);
        }
        return $query->select('product_store.product_id', 'product_store.net_price')->get()->toArray();
    }


    public function getSellersWithOldStoresProductsStock($userId, $productIds, $startCountingDate)
    {
        return OrderProduct::query()
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('stores', 'stores.id', '=', 'orders.store_id')
            ->where('stores.user_id', $userId)
            ->where('orders.status_id', AOrders::RECEIVED)
            ->where('delivery_date', '>=', $startCountingDate)
            ->where('orders.user_id', '!=', $userId)
            ->whereIn('order_products.product_id', $productIds)
            ->select('orders.user_id as owner_id', DB::raw('(order_products.purchased_item_count * order_products.basic_unit_count) as stock'), 'order_products.item_price as product_price', 'order_products.product_id')
            ->get()->toArray();
    }

    public function getFollowedStoresCount($userId): int
    {
        return FollowedStore::query()
            ->select('id')
            ->where('user_id', $userId)
            ->count('*');
    }

    public function getStoreFollowersCount($storeId)
    {
        return FollowedStore::query()->where('store_id', $storeId)->count();
    }

    public function getStoreRatings($storeId)
    {
        return StoreRate::query()->where('store_id', $storeId)
            ->select(DB::raw('SUM(amount) as rating'), DB::raw('COUNT(*) as reviews_count'))
            ->get();
    }

    public function syncBadges($data)
    {
        try {
            $store = $this->model->newQuery()->find($data['id']);

            $store->badges()->sync($data['badges']);

        } catch (\Exception $e) {

            DB::rollBack();
        }
    }

    public function updateAuthData($data)
    {
        $fields = [
            'email' => $data['email'],
            'mobile' => $data['mobile'],
        ];
        if (isset($data['password'])) {
            $fields ['password'] = bcrypt($data['password']);
        }
        User::query()->where('id', $data['owner_id'])
            ->update($fields);
        Store::query()->where('user_id', $data['owner_id'])
            ->update([
                'mobile' => $data['mobile'],
            ]);
    }

    public function rateStoreByAdmin($request)
    {
        $userId = UserId::UserId($request);
        SellerRate::updateOrCreate(
            ['rater_type' => User::class, 'rater_id' => $userId,
                'rated_type' => Store::class, 'rated_id' => $request->store_id,],
            ['rate' => $request->rate, 'review' => $request->review]
        );
    }
}
