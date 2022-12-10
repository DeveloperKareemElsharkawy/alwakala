<?php

namespace App\Lib\Helpers\UserId;

use App\Enums\UserTypes\UserType;
use App\Models\Seller;
use App\Models\Store;

class UserId
{

    public static function UserId($request)
    {
        $user = $request->user('api');
        $userId = null;

        if ($user) {
            if ($user->type_id == UserType::ADMIN)
                return $user->id;

            $seller = Seller::query()->where('user_id', $user->id)->first();
            $store = Store::query()->where('id', $seller->store_id)->first();
            $userId = $store->user_id;
        }

        return $userId;
    }

    public static function GetUserIdFromStore($storeID)
    {
        $store = Store::query()->find($storeID);

        $userId = null;
        if ($store)
            $userId = $store['user_id'];

        return $userId ;
    }

}
