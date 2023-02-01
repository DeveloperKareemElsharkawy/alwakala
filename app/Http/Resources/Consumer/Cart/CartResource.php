<?php

namespace App\Http\Resources\Consumer\Cart;


use App\Lib\Helpers\Coupon\CouponService;
use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            // 'id' => $this['id'],
            'stores' => CartStoresResource::collection($this['stores']),
            'cart_total' => intval(($this['cart_total'] * 100)) / 100,
            'cart_count' => $this['cart_count'],
            'item_count' => $this['item_count'],
            'has_coupon' => $this['coupon']['has_coupon'],
            'coupon' => [
                'coupon_name' => $this['coupon']['coupon_name'],
                'coupon_code' => $this['coupon']['coupon_code'],
                'coupon_products_count' => $this['coupon']['coupon_products_count'],
                'coupon_products' => $this['coupon']['coupon_products'],
                'discountAmount' => $this['coupon']['discountAmount'],
                'couponAmount' => $this['coupon']['couponAmount'],
                'discountType' => $this['coupon']['discountType'],
            ],

            'has_share_coupon' => (bool)count($this->getShareCoupon($request, 'share_coupon_participants')),
            'share_coupon_participants' => $this->getShareCoupon($request, 'share_coupon_participants'),
            'share_coupon_discount' => $this->getShareCoupon($request, 'share_coupon_discounts'),
        ];
    }

    public function getShareCoupon($request, $type)
    {
        $cart = Cart::query()->where('user_id', $request->user_id)->first();

        $coupon = Coupon::with('coupon_products', 'discounts')
            ->find($cart->coupon_id);

        if ($type == 'share_coupon_participants') {
            if ($coupon && $cart->share_coupon_code) {
                return CouponService::getShareCoupon($cart->share_coupon_code, $coupon)['share_coupon'];
            }
        }

        if ($type == 'share_coupon_discounts') {
            if ($coupon && $cart->share_coupon_code) {
                return CouponService::getShareCoupon($cart->share_coupon_code, $coupon)['coupon_discount'];
            }
        }
        return [];
    }
}
