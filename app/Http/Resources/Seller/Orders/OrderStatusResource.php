<?php

namespace App\Http\Resources\Seller\Orders;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $lang = LangHelper::getDefaultLang($request);

        return [
            "id" => $this->id,
            "status" => $this['status_' . $lang],
        ];
    }
}
