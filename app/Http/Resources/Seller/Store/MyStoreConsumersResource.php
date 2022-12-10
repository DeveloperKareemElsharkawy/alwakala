<?php

namespace App\Http\Resources\Seller\Store;

use App\Enums\Orders\AOrders;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyStoreConsumersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {

        $ordersIds = Order::query()->where('user_id', $request->user_id)->pluck('id')->toArray();

        $orderedProducts = OrderProduct::query()->where('store_id', $this['id'])->whereIn('order_id', $ordersIds)->get();

        return [
            "id" => $this['id'],
            "store_type_id" => $this['store_type_id'],
            "name" => $this['name'],
            "rate" => $this['rate'],
            "logo" => $this['logo'],
            "is_followed" => $this['is_followed'],
            "number_of_followers" => $this['number_of_followers'],
            "number_of_views" => $this['number_of_views'],
            'orders_count' => count($orderedProducts),
            'returns_on_orders' => count($orderedProducts->where('status_id', AOrders::RETURNED)),
            'purchases_on_orders' => array_sum($orderedProducts->whereNotIn('status_id', [AOrders::RETURNED, AOrders::CANCELED, AOrders::CANCELED])->pluck('quantity')->toArray()),

        ];
    }

}
