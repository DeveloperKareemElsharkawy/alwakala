<?php

namespace App\Http\Resources\Seller\Store;

use App\Http\Resources\Seller\ProductResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreCardResource extends JsonResource
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

        ];
    }
}
