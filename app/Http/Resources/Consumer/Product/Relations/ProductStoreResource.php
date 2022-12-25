<?php

namespace App\Http\Resources\Consumer\Product\Relations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStoreResource extends JsonResource
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
             'id' => $this->id,
             'name' => $this->name,
             'logo' => $this->logo ? config('filesystems.aws_base_url') . $this->logo : null,
        ];
    }
}
