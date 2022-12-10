<?php

namespace App\Http\Resources\Dashboard\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            "image" => config('filesystems.aws_base_url') . $this->image,
            "activation" => $this->activation,
            "is_seller" => $this->is_seller,
            "is_consumer" => $this->is_consumer,
            "packing_unit" => $this->packing_unit,
            "parent_name" => $this->parent?$this->parent->name_en." - ".$this->parent->name_ar:"",
            "parent_parent_name" => ($this->parent)?($this->parent->parent?$this->parent->parent->name_en." - ".$this->parent->parent->name_ar:""):"",
            "parent" => $this->when($this->parent, new CategoryResource($this->parent)),
        ];
    }
}
