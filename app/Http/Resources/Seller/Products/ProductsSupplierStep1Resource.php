<?php

namespace App\Http\Resources\Seller\Products;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsSupplierStep1Resource extends JsonResource
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
            "name" => $this->name,
            "description" =>  $this->description,
            "activation" => $this->activation,
            "category_id" => $this->category_id,
            "brand_id" => $this->brand_id,
            "owner_id" => $this->owner_id,
            "channel" => $this->channel,
            "consumer_price" => $this->consumer_price,
            "unit_id" => $this->unit_id,
            "material_id" => $this->material_id,
            "material_rate" => $this->material_rate,
            "shipping_method_id" => $this->shipping_method_id,
            "policy_id" => $this->policy_id,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
            "id" => $this->id,
            "bundle_count" => $this->PackingUnitProduct->basic_unit_count,
            "item_price" => $this->productStore->price,
            "bundle_price" => $this->productStore->price * $this->PackingUnitProduct->basic_unit_count,
        ];
    }
}
