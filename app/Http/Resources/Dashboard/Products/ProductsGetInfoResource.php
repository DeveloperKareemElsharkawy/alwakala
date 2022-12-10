<?php

namespace App\Http\Resources\Dashboard\Products;

use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Rate\RateHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsGetInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lang = LangHelper::getDefaultLang($request);

        return [
            "id" => $this->id,
            "name" =>  $this->name,
            "description" =>  $this->description,
            "brand_id" => $this->brand_id,
            "policy_id" => $this->policy_id,
            "material_id" => $this->material_id,
            "shipping_method_id" => $this->shipping_method_id,
            "category_id" => $this->category_id,
            "owner_id" => $this->owner_id,
            "consumer_price" => $this->consumer_price,
            "activation" => $this->activation,
            "rate" => (new RateHelper)->getAverageRate($this->id, Product::class),
            "brand" => $this->brand,
            "category" => $this->category,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
            "owner" => $this->owner,
            "material" => $this->material,
            "shipping_method" => $this->shipping_method,
            "policy" => $this->policy,
            "publish_app_at" => $this->when($this->productStore,  $this->productStore->publish_app_at),
            "price" => $this->when($this->productStore,  $this->productStore->price),
            "discount" => $this->when($this->productStore,  $this->productStore->discount),
            "free_shipping" => $this->when($this->productStore,  $this->productStore->free_shipping),
            "product_attributes" => $this->when($this->packingUnitProduct,  ProductAttributesResource::collection($this->packingUnitProduct->attributes))
            
        ];
    }
}
