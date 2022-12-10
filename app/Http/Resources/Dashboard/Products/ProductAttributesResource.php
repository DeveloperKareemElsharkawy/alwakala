<?php

namespace App\Http\Resources\Dashboard\Products;

use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Rate\RateHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributesResource extends JsonResource
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
            "quantity" =>  $this->quantity,
            "size" => new ProductAttributesSizeResource($this->size)
        ];
    }
}
