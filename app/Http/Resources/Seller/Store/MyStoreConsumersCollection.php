<?php

namespace App\Http\Resources\Seller\Store;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MyStoreConsumersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     */

    public $collects = MyStoreConsumersResource::class;

    public function toArray($request): array
    {
        return ['status'=>true  ,'message' => trans('messages.general.listed'),'data' => $this->collection ];
    }
}
