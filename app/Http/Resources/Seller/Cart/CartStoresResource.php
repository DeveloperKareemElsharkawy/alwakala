<?php

namespace App\Http\Resources\Seller\Cart;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartStoresResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this['name'],
            'logo' => $this['logo'] ? config('filesystems.aws_base_url') . $this['logo'] : null,
            'total_quantity' => $this['total_quantity'],
            'total_item_quantity' => $this['total_item_quantity'],
            'total_Price' => $this['total_Price'],
            'products' => CartItemResource::collection($this['items']),
        ];
    }
}
