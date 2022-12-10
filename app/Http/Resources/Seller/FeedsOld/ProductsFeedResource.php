<?php

namespace App\Http\Resources\Seller\FeedsOld;

use App\Models\ProductStore;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsFeedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $productStore = ProductStore::query()
            ->where([['store_id', $this->pivot->store_id], ['product_id', $this->pivot->product_id]])->first();


        return [
            'store_id' => $this->pivot->store_id,
            'product_id' => $this->id,
            'product_name' => $this->name,
            'product_image' => $this->image ? config('filesystems.aws_base_url') . $this->image->image : null,
            'product_price' => $productStore->price,
            'product_net_price' => $productStore->net_price,
            'category_id' => $this->category_id,
            'product_discount' => $productStore->discount,
            'created_at' => date_format($this->created_at, "Y-m-d H:i:s") ?? '',

        ];
    }
}
