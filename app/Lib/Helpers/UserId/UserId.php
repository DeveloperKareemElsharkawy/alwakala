<?php

namespace App\Lib\Helpers\UserId;

use App\Enums\UserTypes\UserType;
use App\Models\Seller;
use App\Models\Store;
use App\Models\User;

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

        return $userId;
    }

    public static function GetShareCodeByUserId($userId)
    {
        $user = User::query()->find($userId);

        if (!$userId)
            return null;

        if (!$user->share_coupon_code) {
            $user->share_coupon_code = mt_rand(1, 99) . $user->id . mt_rand(1, 99).str_random(10);
            $user->save();

            return $user->share_coupon_code;
        }
        return $user->share_coupon_code;
    }

}
