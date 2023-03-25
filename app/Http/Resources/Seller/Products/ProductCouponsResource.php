<?php

namespace App\Http\Resources\Seller\Products;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCouponsResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'start_date' => date('d-m-Y', strtotime($this->start_date)),
            'end_date' => date('d-m-Y', strtotime($this->end_date)),
            'discounts' => ProductCouponsDiscountsResource::collection($this->discounts),
        ];
    }
}
