<?php

namespace App\Http\Resources\Seller\Payment;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $lang = LangHelper::getDefaultLang($request);;

        return [
            'id' => $this['id'],
            'name' => $this['name_' . $lang],
            'image' => $this['image'] ? config('filesystems.aws_base_url') . $this['image'] : null,
        ];
    }
}
