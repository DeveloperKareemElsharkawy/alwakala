<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            "name" => $this->name,
            "mobile" => $this->mobile,
            "email" => $this->email,
            "image" => config('filesystems.aws_base_url') . $this->image,
            "supplier_id" => $this->supplier->id
        ];
    }
}
