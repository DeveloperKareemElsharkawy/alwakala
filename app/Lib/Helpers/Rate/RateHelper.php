<?php


namespace App\Lib\Helpers\Rate;


use App\Models\Product;
use App\Models\SellerRate;
use Illuminate\Support\Facades\DB;

class RateHelper
{
    public static function storeRatesLimited($itemId, $ratedType)
    {
        return DB::select("SELECT
                        seller_rates.rate,
                        seller_rates.review,
                        seller_rates.created_at,
                        users.name as rated_by
                        From seller_rates
                        JOIN users on users.id = seller_rates.rater_id
                        WHERE rated_id = ?
                        AND rated_type = ?
                        limit ?
                        ", [$itemId, $ratedType, 5]);
    }

    public function getAverageRate($itemId, $ratedType)
    {
        return DB::select("SELECT
                        ROUND(AVG (rate), 1)
                        From seller_rates
                        WHERE rated_id = ?
                        AND rated_type = ?
                        limit ?
                        ", [$itemId, $ratedType, 5])[0]->round;
    }

    public static function getProductAvgRating($storeId, $productId)
    {
        return round(SellerRate::query()->where(['rated_id' => $productId, 'rated_store_id' => $storeId, 'rated_type' => Product::class])->avg('rate'), 1);
    }

}
