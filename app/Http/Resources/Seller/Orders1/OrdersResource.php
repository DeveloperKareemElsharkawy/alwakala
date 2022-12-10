<?php

namespace App\Http\Resources\Seller\Orders1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\OrderProduct;

class OrdersResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return parent::toArray($request);

    }
}
