<?php

namespace App\Http\Resources\Consumer\Product;

use App\Http\Resources\Consumer\Product\Relations\ProductBrandResource;
use App\Http\Resources\Consumer\Product\Relations\ProductCategoryResource;
use App\Http\Resources\Consumer\Product\Relations\ProductMaterialResource;
use App\Http\Resources\Consumer\Product\Relations\ProductPolicyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "youtube_link" => $this->youtube_link,

            'policy' => new ProductPolicyResource($this->policy),
            'category' => new ProductCategoryResource($this->category),
            'brand' => new ProductBrandResource($this->brand),
            'material' => new ProductMaterialResource($this->material),
            'shipping' => new ProductShippingResource($this->shipping),

        ];
    }
}
