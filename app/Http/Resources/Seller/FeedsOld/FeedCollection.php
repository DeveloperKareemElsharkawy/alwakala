<?php

namespace App\Http\Resources\Seller\FeedsOld;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class FeedCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */

    public $collects = FeedsResource::class;

    public function toArray($request)
    {
        return ['status'=>true  ,'message' => trans('messages.sections.feeds_list'),'data' => $this->collection ];
    }
}
