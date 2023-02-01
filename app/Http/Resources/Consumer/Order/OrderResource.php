<?php

namespace App\Http\Resources\Consumer\Order;

use App\Http\Resources\Consumer\Order\Relations\AddressResource;
use App\Http\Resources\Consumer\Order\Relations\PaymentMethodResource;
use App\Http\Resources\Consumer\Order\Relations\StatusResource;
use App\Http\Resources\Consumer\Store\OrderStoreProductsResource;
use App\Lib\Helpers\Coupon\CouponService;
use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $coupon = Coupon::with('coupon_products', 'discounts')
            ->find($this->coupon_id);

        $couponData = json_decode($this->coupon_data);

        return [
            'id' => $this->id,
            'order_number' => $this->number,
            'total_price' => (float)$this->total_price,
            'discount' => (float)$this->discount,
            'status' => new StatusResource($this->last_status),
            'has_coupon' => $couponData?->{'has_coupon'},
            'coupon' => $couponData,
            'payment_method' => new PaymentMethodResource($this->payment_method),
            'address' => new AddressResource($this->order_address),
            'items' =>  OrderStoreProductsResource::collection($this->orderItems()),
            'has_share_coupon' => (bool)$this->share_coupon_code,
            'pending_coupon_participants' => $this->getPendingCouponParticipants($request, $coupon),
            'active_coupon_participants' => $this->getActiveCouponParticipants($request, $coupon),

        ];
    }


    public function orderItems(): array
    {
        $orderItems = [];
        $storeLoop = 0;

        foreach ($this->items->groupBy('store_id') as $storeId => $items) { // Group Order Items by Store

            $orderItems[$storeLoop] = $items[0]['store'];
            $orderItems[$storeLoop]['items'] = collect($items);

            $storeLoop++;
        }
        return collect($orderItems)->values()->all();
    }

    public function getPendingCouponParticipants($request, $coupon)
    {
        if ($coupon && $this->share_coupon_code) {
            return CouponService::getPendingCouponParticipants($this->share_coupon_code, $this->coupon_id);
        }
        return [];
    }

    public function getActiveCouponParticipants($request, $coupon)
    {
        if ($coupon && $this->share_coupon_code) {
            return CouponService::getActiveCouponParticipants($this->share_coupon_code, $this->coupon_id);
        }
        return [];
    }

    public function shareCouponDiscounts($request, $coupon)
    {
        if ($coupon && $this->share_coupon_code) {
            return CouponService::getPendingCouponParticipants($this->share_coupon_code, $this->coupon_id)['coupon_discount'];
        }
        return [];
    }

}
