<?php

namespace App\Http\Resources\Seller\Brands;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandsResource extends JsonResource
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
            'name' => $this['name_' . $lang] ?? '',
            'image' => $this->image ? config('filesystems.aws_base_url') . $this->image : null,
        ];
    }
}
