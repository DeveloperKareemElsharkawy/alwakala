<?php

namespace App\Http\Resources\Seller\Store;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SellerRateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     */

    public $collects = SellerRateResource::class;

    public function toArray($request)
    {
        return ['status' => true, 'message' => trans('messages.general.listed'), 'data' => $this->collection];
    }
}
