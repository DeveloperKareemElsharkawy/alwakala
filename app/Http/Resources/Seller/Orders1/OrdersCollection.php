<?php

namespace App\Http\Resources\Seller\Orders1;

use App\Http\Resources\Seller\Orders\OrdersResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return
     * array
     */

    public $collects = OrdersResource::class;

    public function toArray($request)
    {
        return [
            'status' => true,
            'message' => trans('messages.order.retrieved_all'),
            'data' => $this->collection
        ];
    }

}
