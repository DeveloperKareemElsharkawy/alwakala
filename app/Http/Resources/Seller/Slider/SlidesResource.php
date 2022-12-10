<?php

namespace App\Http\Resources\Seller\Slider;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlidesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     *
     */
    public function toArray($request): array
    {
        $lang = LangHelper::getDefaultLang($request);

        return [
            "id" => $this['id'],
            "title" => $this['title_' . $lang],
            "description" => $this['description_' . $lang],
            "web_image" => config('filesystems.aws_base_url') . $this['web_image'],
            "mobile_image" => config('filesystems.aws_base_url') . $this['mobile_image'],
            "type_id" => $this['item_type'],
            "store_id" => $this->storeId(),
            "type_name" => $this['type']['type_' . $lang],
            "item_id" => $this['item_id'] ? (int)$this['item_id'] : null,
        ];
    }

    public function storeId()
    {
        if ($this['item_type'] == 1) {
            $product = Product::query()->with('productStore')->has('productStore')->find($this['item_id']);
            if ($product && $product['productStore']){
                return $product['productStore']['store_id'];
            }
        }
        return null;
    }
}
