<?php

namespace App\Http\Resources\Seller\Offers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "image" => $this->image ? config('filesystems.aws_base_url') . $this->image->image : null ,
        ];
    }
}
