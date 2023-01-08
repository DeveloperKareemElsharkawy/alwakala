<?php

namespace App\Http\Resources\Consumer\Order;

use App\Http\Resources\Consumer\Order\Relations\AddressResource;
use App\Http\Resources\Consumer\Order\Relations\PaymentMethodResource;
use App\Http\Resources\Consumer\Order\Relations\ProductResource;
use App\Http\Resources\Consumer\Order\Relations\StatusResource;
use App\Http\Resources\Consumer\Product\Relations\ProductColorResource;
use App\Http\Resources\Seller\Sizes\SizesResource;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
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
            'order_product_id' => $this->id,
            'item_price' => (float)$this->item_price,
            'total_price' => (float)$this->total_price,
            'purchased_item_count' => (int)$this->purchased_item_count,
            'size' => new SizesResource($this->size),
            'color' => new ProductColorResource($this->color),
            'product' => new ProductResource($this->product),
            'status' =>  new StatusResource($this->status),
        ];
    }
}
