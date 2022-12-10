<?php

namespace App\Http\Resources\Seller\AppTv;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class AppTvResource extends JsonResource
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

        return [
            "id" => $this->id,
            "title" => $this['title_' . $lang],
            "description" => $this['description_' . $lang],
            "web_image" => $this->mobile_image ? config('filesystems.aws_base_url') . $this->web_image : null,
            "mobile_image" => $this->mobile_image ? config('filesystems.aws_base_url') . $this->mobile_image : null,
            "category_id" => $this->category_id,
            "item_type" => $this->item_type,
            "item_id" => $this->item_id,
            "items_ids" => $this->items_ids,
        ];
    }
}
