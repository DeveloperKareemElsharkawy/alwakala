<?php

namespace App\Http\Resources\Consumer\Cart;

use App\Http\Resources\Seller\Locations\AddressResource;
use App\Http\Resources\Seller\Payment\PaymentMethodResource;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsumerCartSummaryResource extends JsonResource
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
            'address' => [new AddressResource($this['address'])],
            'payment' => [new PaymentMethodResource($this['payment_method'])],
            'cart' => new CartResource($this['carts']),
        ];
    }
}
