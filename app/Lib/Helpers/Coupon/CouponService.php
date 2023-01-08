<?php

namespace App\Lib\Helpers\Coupon;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Store;
use Carbon\Carbon;

class CouponService
{

    public static function ApplyCoupon($couponCode, $request, $cartItems): array
    {
        $coupon = Coupon::where('code', $couponCode)->with('coupon_products', 'discounts')
            ->first();

        $couponProducts = static::CheckCouponProducts($coupon, $cartItems);

        if (!$couponProducts['has_coupon']) {
            return ['status' => false, 'message' => $couponProducts['message']];
        }

        Cart::where('user_id', $request->user_id)->update([
            'coupon_id' => $coupon->id,
            'coupon_name' => $coupon->name,
            'coupon_code' => $coupon->code,

        ]);

        return ['status' => true, 'discount' => $couponProducts];
    }

    public static function getCouponDiscount($couponId, $request, $cartItems): array
    {
        $coupon = Coupon::query()->with('coupon_products', 'discounts')
            ->find($couponId);

        $couponProducts = static::CheckCouponProducts($coupon, $cartItems);

        if (!$couponProducts['has_coupon']) {
            return [
                'coupon_name' => null,
                'coupon_code' => null,
                'has_coupon' => false,
                'coupon_products_count' => 0,
                'coupon_products' => [],
                'discountAmount' => 0,
                'couponAmount' => 0,
                'discountType' => 0
            ];
        }

        return $couponProducts;
    }

    private static function CheckCouponProducts($coupon, $cartItems): array
    {
        $totalPrice = 0.0;
        $discountAmount = 0.0;
        $cartItemsFound = [];
        $couponProducts = [];

        if (!$coupon) {
            return ['has_coupon' => false, 'message' => trans('messages.coupon.not_found')];
        }

        if (!Carbon::now()->between($coupon->start_date, $coupon->end_date)) {
            return ['has_coupon' => false, 'message' => trans('messages.coupon.ended')];
        }

        if (!$coupon->active) {
            return ['has_coupon' => false, 'message' => trans('messages.coupon.inactive')];
        }

        if ($coupon->quantity == 0 && !$coupon->unlimited) {
            return ['has_coupon' => false, 'message' => trans('messages.coupon.qty_is_over')];
        }

        $cartsItems = collect($cartItems)->flatten()->pluck('items')->collapse(); // Collect Cart Items We use $cartItems So We Can Get Price From Cart

        foreach ($cartsItems as $cartItem) {
            foreach ($coupon->coupon_products as $couponProduct) {
                if ($cartItem->product_id == $couponProduct->product_id && $cartItem->store_id == $couponProduct->store_id) {
                    $cartItemsFound[] = $cartItem;
                    $couponProducts[] = $cartItem?->product_store?->product->name;
                    $totalPrice += $cartItem->sub_total;
                }
            }
        }

        if (!count($cartItemsFound)) {
            return ['has_coupon' => false, 'message' => trans('messages.coupon.no_products_for_this_coupon')];
        }

        $couponAvailableDiscounts = collect($coupon->discounts->where('amount_from', '<=', $totalPrice)
            ->where('amount_to', '<=', $totalPrice));

        $couponDiscount = $couponAvailableDiscounts->sortByDesc('amount_to')->first();

        if (!$couponDiscount) {
            return ['has_coupon' => false, 'message' => trans('messages.coupon.purchase_amount')];
        }

        if ($couponDiscount->discount_type == DiscountTypes::AMOUNT) {
            $discountAmount = $couponDiscount->discount;
        } elseif ($couponDiscount->discount_type == DiscountTypes::PERCENTAGE) {
            $discountAmount = number_format(($couponDiscount->discount * $totalPrice) / 100, 2);
        }

        return [
            'coupon_name' => $coupon->name,
            'coupon_code' => $coupon->code,
            'has_coupon' => true,
            'coupon_products_count' => count($cartItemsFound),
            'coupon_products' => $couponProducts,
            'discountAmount' => (float)$discountAmount,
            'couponAmount' => $couponDiscount->discount,
            'discountType' => DiscountTypes::getDiscountType($couponDiscount->discount_type)
        ];
    }

    public static function OrderProductsCheck($couponId, $orderItems)
    {
         $coupon = Coupon::query()->with('coupon_products', 'discounts')->find($couponId);
        $totalPrice = 0.0;
        $discountAmount = 0.0;
        $cartItemsFound = [];
        $couponProducts = [];

        foreach ($orderItems as $orderItem) {
            foreach ($coupon->coupon_products as $couponProduct) {

                if ($orderItem->product_id == $couponProduct->product_id && $orderItem->store_id == $couponProduct->store_id) {

                    $cartItemsFound[] = $orderItem;
                    $couponProducts[] = $orderItem?->product?->name;
                    $totalPrice += $orderItem->total_price;
                }
            }
        }

        if (!count($cartItemsFound)) {
            return [
                'coupon_name' => null,
                'coupon_code' =>null,
                'has_coupon' => false,
                'coupon_products_count' => null,
                'coupon_products' => null,
                'discountAmount' => 0.0,
                'couponAmount' => null,
                'discountType' => null,
            ];
        }

        $couponAvailableDiscounts = collect($coupon->discounts->where('amount_from', '<=', $totalPrice)
            ->where('amount_to', '<=', $totalPrice));

        $couponDiscount = $couponAvailableDiscounts->sortByDesc('amount_to')->first();

        if (!$couponDiscount) {
            return [
                'coupon_name' => null,
                'coupon_code' =>null,
                'has_coupon' => false,
                'coupon_products_count' => null,
                'coupon_products' => null,
                'discountAmount' => 0.0,
                'couponAmount' => null,
                'discountType' => null,
            ];
        }

        if ($couponDiscount->discount_type == DiscountTypes::AMOUNT) {
            $discountAmount = $couponDiscount->discount;
        } elseif ($couponDiscount->discount_type == DiscountTypes::PERCENTAGE) {
            $discountAmount = number_format(($couponDiscount->discount * $totalPrice) / 100, 2);
        }

        return [
            'coupon_name' => $coupon->name,
            'coupon_code' => $coupon->code,
            'has_coupon' => true,
            'coupon_products_count' => count($cartItemsFound),
            'coupon_products' => $couponProducts,
            'discountAmount' => (float)$discountAmount,
            'couponAmount' => $couponDiscount->discount,
            'discountType' => DiscountTypes::getDiscountType($couponDiscount->discount_type)
        ];
    }

}
