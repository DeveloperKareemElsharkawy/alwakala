<?php

namespace App\Http\Resources\Consumer\Order\Relations;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $lang = LangHelper::getDefaultLang($request);

        return [
            "id" => $this->id,
            "name" => $this->name,
            "product_image" => $this->productImage?->{'image_full'},
        ];
    }
}
