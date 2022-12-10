<?php

namespace App\Http\Resources\Dashboard\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name_en" => $this->name_en,
            "name_ar" => $this->name_ar,
            "description" => $this->description,
            "image" => config('filesystems.aws_base_url') . $this->image,
            "activation" => $this->activation,
            "is_seller" => $this->is_seller,
            "is_consumer" => $this->is_consumer,
            "packing_unit" => $this->packing_unit,
            "parent" => $this->when($this->parent, new CategoryResource($this->parent)),
        ];
    }
}
