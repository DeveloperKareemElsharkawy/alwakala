<?php

namespace App\Http\Resources\Consumer\Product\Relations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {

        return [
            "image" => config('filesystems.aws_base_url') . $this->image,
            'color_id' => $this->color_id
        ];
    }
}
