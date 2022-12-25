<?php


namespace App\Lib\Helpers\Product;


use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Models\PackingUnitProduct;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;

class ProductVariationHelper
{


    public static function productStatus($availableStock, $publishDate, $reviewed)
    {
        $productStatuses = '';
        if (!$reviewed) {
            $productStatuses = trans('messages.status.in_review');
        } elseif ($availableStock > 0 && Carbon::now()->greaterThan($publishDate)) {
            $productStatuses = trans('messages.status.available');
        } elseif ($availableStock == 0) {
            $productStatuses = trans('messages.status.not_available');
        } elseif ($publishDate >= Carbon::now()->lessThan($publishDate)) {
            $productStatuses = trans('messages.status.soon');
        }
        return $productStatuses;
    }

    public static function calculateProductDiscount($discountType, $discountValue, $price)
    {
        if ($discountType == DiscountTypes::AMOUNT) {
            return $price - $discountValue;
        } else {
            return $price - (($price / 100) * $discountValue);
        }
    }

    public static function isActiveProduct($productId): bool
    {
        $product = Product::query()
            ->select('activation')
            ->find($productId);

        if ($product->activation == false) {
            return false;
        }
        return true;

    }

    public static function canShowPrice($userId, $activation, $price)
    {
        $user = User::query()->where('id', $userId)->first();
        if ($user) {
            if ($user->type_id == UserType::CONSUMER) {
                return $price;
            }
        }
        if (!$userId || !$activation) {
            return '--';
        } else {
            return $price;
        }
    }

    public static function getProductBasicUnitCount($productId)
    {
        $packingUnit = PackingUnitProduct::query()
            ->where('product_id', '=', $productId)
            ->first();

        if ($packingUnit)
            return $packingUnit->basic_unit_count;

        return false;
    }
}
