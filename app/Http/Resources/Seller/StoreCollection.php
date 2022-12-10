<?php

namespace App\Http\Resources\Seller;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StoreCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     */

    public $collects = StoreResource::class;

    public function toArray($request): array
    {
        return ['status'=>true  ,'message' => trans('messages.general.listed'),'data' => $this->collection ];
    }
}
