<?php

namespace App\Lib\Helpers\Coupon;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Http\Resources\Consumer\ShareCoupon\ShareCouponResource;
use App\Http\Resources\Seller\Coupons\CouponDiscountsResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CartCouponParticipant;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use phpseclib3\File\ASN1\Maps\CountryName;

class CouponService
{

    /**
     * @param $data
     * @param $request
     * @param $cartItems
     * @return array
     */
    public static function ApplyCoupon($data, $request, $cartItems)
    {
        $coupon = Coupon::where('code', $data['coupon_code'])->with('coupon_products', 'discounts')
            ->first();


        if ($data['has_share_coupon_code'] && $coupon->can_share) {
            $user = User::query()->where('id', $request['user_id'])->first();

            $is_main_participant = (bool)$user->share_coupon_code == $data['share_coupon_code'];

            Cart::where('user_id', $user->id)->update(['share_coupon_code' => $data['share_coupon_code']]);

            CartCouponParticipant::query()->firstOrCreate(
                [
                    'coupon_id' => $coupon->id,
                    'user_id' => $user->id,
                    'is_main_participant' => $is_main_participant,
                    'share_coupon_code' => $data['share_coupon_code']
                ],
            );

        }

        $couponProducts = static::CheckCouponProducts($coupon, $cartItems, $data);

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

    /**
     * @param $coupon
     * @param $cartItems
     * @return array
     */
    private static function CheckCouponProducts($coupon, $cartItems, $data): array
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

        $couponAvailableDiscounts = collect($coupon->discounts->where('amount_from', '<=', $totalPrice));

        $couponDiscount = $couponAvailableDiscounts->sortByDesc('amount_from')->first();

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

    /**
     * @param $couponId
     * @param $orderItems
     * @return array
     */
    public static function OrderProductsCheck($couponId, $orderItems): array
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
                'coupon_code' => null,
                'has_coupon' => false,
                'coupon_products_count' => null,
                'coupon_products' => null,
                'discountAmount' => 0.0,
                'couponAmount' => null,
                'discountType' => null,
            ];
        }

        $couponAvailableDiscounts = collect($coupon->discounts->where('amount_from', '<=', $totalPrice));

        $couponDiscount = $couponAvailableDiscounts->sortByDesc('amount_from')->first();

        if (!$couponDiscount) {
            return [
                'coupon_name' => null,
                'coupon_code' => null,
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

    /**
     * @param $couponId
     * @param $request
     * @param $cartItems
     * @return array
     */
    public static function getCouponDiscount($couponId, $request, $cartItems): array
    {
        $coupon = Coupon::query()->with('coupon_products', 'discounts')
            ->find($couponId);


        $cart = Cart::query()->where('user_id', $request['user_id'])->first();

        $couponProducts = static::CheckCouponProducts($coupon, $cartItems, $cart->share_coupon_code);

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

    /**
     * @param $shareCouponCode
     * @param $coupon
     * @return AnonymousResourceCollection|array
     */
    public static function getShareCoupon($shareCouponCode, $coupon): AnonymousResourceCollection|array
    {
        $cartsIds = Cart::query()->where('share_coupon_code', $shareCouponCode)->with('items')->get()->pluck('id')->toArray();

        $cartsItems = CartItem::whereIn('cart_id', $cartsIds)->with('user')->get()->groupBy('user_id');

        $usersLoop = 0;
        $participants = [];
        $participantsTotalPrice = 0.0;

        foreach ($cartsItems as $userId => $cartItems) {

            $products = [];
            $totalPrice = 0.0;

            $participants[$usersLoop] = $cartItems[0]['user'];

            foreach ($coupon->coupon_products as $couponProduct) {
                foreach ($cartItems as $cartItem) {
                    if ($cartItem->product_id == $couponProduct->product_id && $cartItem->store_id == $couponProduct->store_id) {
                        $products[] = $cartItem?->product_store?->product->name;
                        $totalPrice += $cartItem->quantity * (float)$cartItem?->product_store?->consumer_price;
                    }
                }
            }

            $participants[$usersLoop]['products'] = $products;
            $participants[$usersLoop]['total_price'] = $totalPrice;
            $participantsTotalPrice += $totalPrice;
            $usersLoop++;
        }


        $couponAvailableDiscounts = collect($coupon->discounts->where('amount_from', '<=', $participantsTotalPrice));

        $couponDiscount = $couponAvailableDiscounts->sortByDesc('amount_from')->first();


        collect($participants)->map(function ($participant) use ($couponDiscount, $participants) {
            $participant['discount'] = (double)($couponDiscount->discount / count($participants));
            $participant['discount_type'] = DiscountTypes::getDiscountType($couponDiscount->discount_type);
            return $participant;
        });

        return [
            'coupon_discount' => new CouponDiscountsResource($couponDiscount),
            'share_coupon' => count($participants) ? ShareCouponResource::collection($participants) : []
        ];

    }


    /**
     * @param $shareCouponCode
     * @param $couponId
     * @return AnonymousResourceCollection|array
     */
    public static function getPendingCouponParticipants($shareCouponCode, $couponId): AnonymousResourceCollection|array
    {
        $coupon = Coupon::where('id', $couponId)->with('coupon_products', 'discounts')
            ->first();

        $cartsIds = Cart::query()->where('share_coupon_code', $shareCouponCode)->with('items')->get()->pluck('id')->toArray();

        $cartsItems = CartItem::whereIn('cart_id', $cartsIds)->with('user')->get()->groupBy('user_id');

        $usersLoop = 0;
        $participants = [];
        $participantsTotalPrice = 0.0;

        foreach ($cartsItems as $userId => $cartItems) {

            $products = [];
            $totalPrice = 0.0;

            $participants[$usersLoop] = $cartItems[0]['user'];

            foreach ($coupon->coupon_products as $couponProduct) {
                foreach ($cartItems as $cartItem) {
                    if ($cartItem->product_id == $couponProduct->product_id && $cartItem->store_id == $couponProduct->store_id) {
                        $products[] = $cartItem?->product_store?->product->name;
                        $totalPrice += $cartItem->quantity * (float)$cartItem?->product_store?->consumer_price;
                    }
                }
            }

            $participants[$usersLoop]['products'] = $products;
            $participants[$usersLoop]['total_price'] = $totalPrice;
            $participantsTotalPrice += $totalPrice;
            $usersLoop++;
        }


        $couponAvailableDiscounts = collect($coupon->discounts->where('amount_from', '<=', $participantsTotalPrice));

        $couponDiscount = $couponAvailableDiscounts->sortByDesc('amount_from')->first();


        collect($participants)->map(function ($participant) use ($couponDiscount, $participants) {
            $participant['discount'] = (double)($couponDiscount->discount / count($participants));
            $participant['discount_type'] = DiscountTypes::getDiscountType($couponDiscount->discount_type);
            return $participant;
        });

        return [
            'coupon_discount' => new CouponDiscountsResource($couponDiscount),
            'share_coupon' => count($participants) ? ShareCouponResource::collection($participants) : []
        ];

    }


    /**
     * @param $shareCouponCode
     * @param $couponId
     * @return AnonymousResourceCollection|array
     */
    public static function getActiveCouponParticipants($shareCouponCode, $couponId): AnonymousResourceCollection|array
    {
        $coupon = Coupon::where('id', $couponId)->with('coupon_products', 'discounts')
            ->first();

        $ordersIds = Order::query()->where('share_coupon_code', $shareCouponCode)->get()->pluck('id')->toArray();

        $ordersProducts = OrderProduct::whereIn('order_id', $ordersIds)->with('order.user')->get()->groupBy('order_id');


        $usersLoop = 0;
        $participants = [];
        $participantsTotalPrice = 0.0;

        foreach ($ordersProducts as $orderId => $orderProducts) {

            $products = [];
            $totalPrice = 0.0;

            $participants[$usersLoop] = $orderProducts[0]['order']['user'];

            foreach ($coupon->coupon_products as $couponProduct) {
                foreach ($orderProducts as $orderProduct) {
                    if ($orderProduct->product_id == $couponProduct->product_id && $orderProduct->store_id == $couponProduct->store_id) {
                        $products[] = $orderProduct?->product?->name;
                        $totalPrice += $orderProduct->total_price;
                    }
                }
            }

            $participants[$usersLoop]['products'] = $products;
            $participants[$usersLoop]['total_price'] = $totalPrice;
            $participantsTotalPrice += $totalPrice;
            $usersLoop++;
        }


        $couponAvailableDiscounts = collect($coupon->discounts->where('amount_from', '<=', $participantsTotalPrice));

        $couponDiscount = $couponAvailableDiscounts->sortByDesc('amount_from')->first();


        collect($participants)->map(function ($participant) use ($couponDiscount, $participants) {
            $participant['discount'] = (double)($couponDiscount->discount / count($participants));
            $participant['discount_type'] = DiscountTypes::getDiscountType($couponDiscount->discount_type);
            return $participant;
        });

        return [
            'coupon_discount' => new CouponDiscountsResource($couponDiscount),
            'share_coupon' => count($participants) ? ShareCouponResource::collection($participants) : []
        ];

    }

}
