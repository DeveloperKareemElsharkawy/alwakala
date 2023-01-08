<?php

namespace App\Lib\Helpers\StoreId;

use App\Enums\UserTypes\UserType;
use App\Models\Seller;
use App\Models\Store;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;

class StoreId
{

    /**
     * @param $request
     * @return HigherOrderBuilderProxy|mixed|null
     */
    public static function getStoreID($request)
    {
        $user = $request->user('api');
        $storeId = null;

        if ($user) {
            $seller = Seller::query()->where('user_id', $user->id)->first();
            $store = Store::query()->where('id', $seller?->store_id)->first();
            $storeId = $store?->id;
        }

        return $storeId;
    }

    /**
     * @param $storeId
     * @return array
     */
    public static function getStoreUsersIDs($storeId): array
    {
        $store = Store::query()->where('id', $storeId)->first();

        $sellersIds = [];

        if ($store) {
            $sellersIds = Seller::query()->where('store_id', $storeId)->pluck('user_id')->toArray();
        }

        return $sellersIds;
    }

    /**
     * @param $storeIds
     * @return array
     */
    public static function getSellersIDsFromStores($storeIds): array
    {
        $stores = Store::query()->whereIn('id', $storeIds)->get();

        $sellersIds = [];

        if (count($stores) > 0) {
            $sellersIds = Seller::query()->whereIn('store_id', $storeIds)->pluck('user_id')->toArray();
        }

        return $sellersIds;
    }

}
