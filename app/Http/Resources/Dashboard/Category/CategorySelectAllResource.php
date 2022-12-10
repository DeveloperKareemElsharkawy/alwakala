<?php

namespace App\Http\Resources\Dashboard\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySelectAllResource extends JsonResource
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
            "child" => $this->when($this->categories, CategorySelectAllResource::collection($this->categories)),
        ];
    }
}
