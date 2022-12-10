<?php


namespace App\Lib\Helpers\Offers;


use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\UserTypes\UserType;
use App\Lib\Helpers\StoreId\StoreId;
use App\Models\Offer;
use App\Models\OfferStore;
use App\Models\OfferType;
use App\Models\ProductStore;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class OffersHelper
{


    public static function checkOffer($storeId, $orderTotalPurchasedItemsCount, $totalPrice, $userType = UserType::SELLER): ?array
    {
        $query = OfferType::query()->select('id');
        if ($userType == UserType::SELLER) {
            $query->where('name', 'like', '%Supplier to Retailer%');
        } else {
            $query->where('name', 'not like', '%Supplier to Retailer%');
        }

        $offer_type = $query->get()->pluck('id')->toArray();
        $matchedOffer = null;
        $store = StoreRepository::getStoreByStoreId($storeId);

        $activeOffers = Offer::query()->select('id', 'discount_value', 'total_purchased_items', 'discount_type', 'total_price')->whereDate('from', '<=', Carbon::today()->toDateString())
            ->whereDate('to', '>=', Carbon::today()->toDateString())
            ->where('activation', true)
            ->where('max_usage_count', '!=', 0)
            ->where('user_id', $store->user_id)
            ->whereIn('type_id', $offer_type)
            ->where(function ($q) use ($orderTotalPurchasedItemsCount, $totalPrice) {
                $q->where('total_purchased_items', '<=', $orderTotalPurchasedItemsCount)
                    ->Where('total_price', '<=', $totalPrice);
            })
            ->groupBy('id', 'discount_value', 'discount_type', 'total_price', 'total_purchased_items')
            ->orderBy('total_purchased_items', 'desc')
            ->orderBy('total_price', 'desc')
            ->get(); // get first matching offers
        foreach ($activeOffers as $activeOffer) {
            $storeOffer = OfferStore::query()->where('offer_id', $activeOffer->id)->get()->pluck('store_id')->toArray();
            if (!empty($storeOffer) && in_array($storeOffer, $storeId)) {
                $matchedOffer = $activeOffer;
                break;
            } elseif (empty($storeOffer)) {
                $matchedOffer = $activeOffer;
                break;
            }
        }
        if($matchedOffer){
            $discount = 0;
            $netPrice = 0;
            if ($matchedOffer->discount_type == DiscountTypes::AMOUNT)
            {
                $discount = $matchedOffer->discount_value;
                $netPrice = $totalPrice - $discount;
            } else if ($matchedOffer->discount_type == DiscountTypes::PERCENTAGE)
            {
                $discount = ($matchedOffer->discount_value / 100) * $totalPrice;
                $netPrice = $totalPrice - ($matchedOffer->discount_value / 100) * $totalPrice;
            } else{
                // TODO HANDLE DISCOUNT AS GOODS
                return null;
            }

            return [
                'net_price' => $netPrice,
                'discount' => $discount,
                'id' => $matchedOffer->id
            ];
        }
        else {
            return null;
        }
    }


    public static function getPriceFromStoreInventory($request, $offerId, $productId, $userType = UserType::SELLER): array
    {

        $storeId = StoreId::getStoreID($request);

        $offer = Offer::query()->find($offerId);

        $productStore = ProductStore::query()->where([['store_id', $storeId], ['product_id', $productId]])->first();

        $supplierPriceDiscount = 0;
        $consumerPriceDiscount = 0;

        $newSupplierPrice = 0;
        $newConsumerPrice = 0;

        if ($offer->discount_type == DiscountTypes::AMOUNT) {

            $newSupplierPrice = (double)$offer->bulk_price;
            $newConsumerPrice = (double)$offer->retail_price;

            $supplierPriceDiscount = number_format(($offer->bulk_price / $productStore->net_price) * 100, 2) . '%';
            $consumerPriceDiscount = number_format(($offer->retail_price / $productStore->consumer_price) * 100, 2) . '%';

        } elseif ($offer->discount_type == DiscountTypes::PERCENTAGE) {

            $newSupplierPrice = number_format($productStore->net_price - ($productStore->net_price / $offer->discount_value), 2);
            $newConsumerPrice = number_format($productStore->consumer_price - ($productStore->consumer_price / $offer->discount_value), 2);

            $supplierPriceDiscount = $offer->discount_value . '%';
            $consumerPriceDiscount = $offer->discount_value . '%';
        }

        return [
            'original_supplier_price' => (double)$productStore->net_price,
            'original_consumer_price' => (double)$productStore->consumer_price,

            'new_supplier_price' => (double)$newSupplierPrice,
            'new_consumer_price' => (double)$newConsumerPrice,

            'supplier_price_discount' => $supplierPriceDiscount,
            'consumer_price_discount' => $consumerPriceDiscount,
        ];
    }

}
