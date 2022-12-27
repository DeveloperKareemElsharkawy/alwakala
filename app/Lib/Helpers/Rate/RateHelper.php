<?php


namespace App\Lib\Helpers\Rate;


use App\Models\Product;
use App\Models\SellerRate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use LaravelIdea\Helper\App\Models\_IH_SellerRate_C;

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

    public static function getProductAvgRating($storeId, $productId): float
    {
        return round(SellerRate::query()->where(['rated_id' => $productId, 'rated_store_id' => $storeId, 'rated_type' => Product::class])->avg('rate'), 1);
    }

    public static function getStoreAvgRating($storeId): float
    {
        return round(SellerRate::query()->where(['rated_id' => $storeId, 'rated_type' => Store::class])->avg('rate'), 1);
    }

    public static function getProductReviews($storeId, $productId, $limit = 3, $withPagination = false): Collection|LengthAwarePaginator|_IH_SellerRate_C|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        if ($withPagination) {
            return SellerRate::query()->where(['rated_id' => $productId, 'rated_store_id' => $storeId, 'rated_type' => Product::class])->paginate(10);
        }

        return SellerRate::query()->where(['rated_id' => $productId, 'rated_store_id' => $storeId, 'rated_type' => Product::class])->limit($limit)->get();
    }
}
