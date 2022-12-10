<?php

namespace App\Http\Resources\Seller\Feeds;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FeedsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     */

    public $collects = FeedsResource::class;

    public function toArray($request): array
    {
        return ['status'=>true  ,'message' => trans('messages.sections.feeds_list'),'data' => $this->collection ];
    }
}
