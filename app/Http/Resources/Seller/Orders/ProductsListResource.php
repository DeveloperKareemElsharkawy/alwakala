<?php

namespace App\Http\Resources\Seller\Orders;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $lang = LangHelper::getDefaultLang($request);

        $size = Size::query()->with('sizeType')->find($this->pivot->size_id);

        $color = Color::query()->find($this->pivot->color_id);

        return [
            "id" => $this->id,
            "name" => $this->name,
            "image" => $this->productImage['image_full'] ?? null,
            "size" => $size?->size,
            "size_type" => $size?->sizeType->{'type_'.$lang},
            "color" => $color ? $color['name_' . $lang] : '',
            "purchased_item_count" => $this->pivot->purchased_item_count,
            "item_price" => $this->pivot->item_price,
            "total_price" => $this->pivot->total_price,

        ];
    }
}
