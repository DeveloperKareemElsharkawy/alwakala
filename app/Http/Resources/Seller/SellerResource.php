<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class SellerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name"=> $this->name,
            "email" => $this->email,
            "mobile" => $this->mobile,
            "image" => $this->image ? config('filesystems.aws_base_url') . $this->image : null

        ];
    }
}
