<?php

namespace App\Http\Resources\Seller\Feeds;

use App\Models\ProductStore;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsFeedsResource extends JsonResource
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
            'id' => $this['product_id'],
            'name' => $this['product']['name'],
            'color_id' =>$this['product']['image'] ? $this['product']['image']['color_id']  : null,
            'image' => $this['product']['image'] ? config('filesystems.aws_base_url') . $this['product']['image']['image'] : null,
            'price' => $this['net_price'],
        ];
    }
}
