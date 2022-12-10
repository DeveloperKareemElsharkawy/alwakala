<?php

namespace App\Http\Resources\shared;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "mobile" => $this->mobile,
            "type_id" => $this->type_id,
            "image" => config('filesystems.aws_base_url') .  $this->image
        ];
    }
}
