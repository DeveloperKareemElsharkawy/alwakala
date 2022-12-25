<?php

namespace App\Http\Resources\Consumer\Product;

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
            'category' => new ProductCategoryResource($this->category),
            'brand' => new ProductBrandResource($this->brand),
         ];
    }
}
