<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivitiesResource extends JsonResource
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
            "action" => trans('messages.actions.'.$this->action),
            "ref_id" => $this->ref_id,
            "user_id" => $this->user_id,
            "type" => $this->type,

        ];
    }
}
