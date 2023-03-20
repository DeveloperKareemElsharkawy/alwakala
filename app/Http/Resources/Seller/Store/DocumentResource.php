<?php

namespace App\Http\Resources\Seller\Store;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            "logo" => $this->logo ? config('filesystems.aws_base_url') . $this->logo : "",
            "licence" => $this->logo ? config('filesystems.aws_base_url') . $this->licence : "",
            "cover" => $this->logo ? config('filesystems.aws_base_url') . $this->cover : "",

            "is_verified_logo" => ($this->is_verified_logo === null) ? 'pending' : (($this->is_verified_logo == 1) ? 'accepted' : 'rejected'),
            "is_verified_cover" => ($this->is_verified_cover === null) ? 'pending' : (($this->is_verified_cover == 1) ? 'accepted' : 'rejected'),
            "is_verified_licence" => ($this->is_verified_licence === null) ? 'pending' : (($this->is_verified_licence == 1) ? 'accepted' : 'rejected'),

        ];
    }
}
