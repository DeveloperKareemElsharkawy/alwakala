<?php

namespace App\Http\Resources\Seller\Sizes;


use Illuminate\Http\Resources\Json\JsonResource;

class SizesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'size' => $this->size,
        ];
    }
}
