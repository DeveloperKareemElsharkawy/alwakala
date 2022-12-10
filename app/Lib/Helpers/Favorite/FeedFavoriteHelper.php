<?php

namespace App\Lib\Helpers\Favorite;

use App\Lib\Helpers\UserId\UserId;
use App\Models\Feed;
use App\Models\SellerFavorite;
use App\Models\User;
use App\Models\View;

class FeedFavoriteHelper
{

    /**
     * @param $userId
     * @param $feedId
     * @param $storeId
     * @return bool
     */
    public static function isFavorite($userId , $feedId, $storeId = null)
    {
        $sellerFavorite = SellerFavorite::query()
            ->where('favoriter_type', '=', User::class)
            ->where('favoriter_id', '=', $userId)
            ->where('favorited_type', '=', Feed::class)
            ->where('favorited_id', '=', $feedId)
            ->where('store_id', '=', $storeId)
            ->first();

        return (bool)$sellerFavorite;
    }


}
