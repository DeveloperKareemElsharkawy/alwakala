<?php

namespace App\Http\Resources\Seller\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreMiniDataResource extends JsonResource
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
            "id" => $this['id'],
            "name" => $this['name'],
            "logo" => $this['logo'] ? config('filesystems.aws_base_url') . $this['logo'] : null,
            "store_type_id" => $this['store_type_id'],
        ];
    }
}
