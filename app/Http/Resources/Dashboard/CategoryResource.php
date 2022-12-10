<?php

namespace App\Http\Resources\Dashboard;

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
//            "priority" => $this->priority,
            "category" => $this->parent ? $this->parent->name_ar : 'Parent'
        ];
    }
}
