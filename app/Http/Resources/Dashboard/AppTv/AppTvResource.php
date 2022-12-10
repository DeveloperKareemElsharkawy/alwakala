<?php

namespace App\Http\Resources\Dashboard\AppTv;

use Illuminate\Http\Resources\Json\JsonResource;

class AppTvResource extends JsonResource
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
            "item_id" => $this->item_id,
            "app_name" => $this->app->app_en,
            "type" => $this->type->type_en,
            "expiry_date" => $this->expiry_date,
            "image" => config('filesystems.aws_base_url') . $this->image,
//            "location" => $this->category_id ? 'category' : $this->store_id ? 'store' : 'home'
        ];
    }
}
