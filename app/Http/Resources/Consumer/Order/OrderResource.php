<?php

namespace App\Http\Resources\Consumer\Order;

use App\Http\Resources\Consumer\Order\Relations\AddressResource;
use App\Http\Resources\Consumer\Order\Relations\PaymentMethodResource;
use App\Http\Resources\Consumer\Order\Relations\StatusResource;
use App\Http\Resources\Consumer\Store\OrderStoreProductsResource;
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
        $coupon = json_decode($this->coupon_data);

        return [
            'id' => $this->id,
            'order_number' => $this->number,
            'total_price' => (float)$this->total_price,
            'discount' => (float)$this->discount,
            'status' => new StatusResource($this->last_status),
            'has_coupon' => $coupon?->{'has_coupon'},
            'coupon' => $coupon,
            'payment_method' => new PaymentMethodResource($this->payment_method),
            'address' => new AddressResource($this->order_address),
            'items' =>  OrderStoreProductsResource::collection($this->orderItems()),
        ];
    }


    public function orderItems()
    {
        $orderItems = [];
        $storeLoop = 0;

        foreach ($this->items->groupBy('store_id') as $storeId => $items) {

            $orderItems[$storeLoop] = $items[0]['store'];
            $orderItems[$storeLoop]['items'] = collect($items);

            $storeLoop++;

        }

        return collect($orderItems)->values()->all();
    }
}
