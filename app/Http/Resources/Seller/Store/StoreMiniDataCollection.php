<?php

namespace App\Http\Resources\Seller\Store;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class StoreMiniDataCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */

    public $collects = StoreOpeningHoursResource::class;

    public function toArray($request): array
    {
        return ['status'=>true  ,'message' => trans('messages.general.listed'),'data' => $this->collection ];
    }
}
