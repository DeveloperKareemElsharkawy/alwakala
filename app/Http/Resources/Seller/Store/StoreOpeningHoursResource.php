<?php

namespace App\Http\Resources\Seller\Store;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreOpeningHoursResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $lang = LangHelper::getDefaultLang($request);
        return [
            "id" => $this['id'],
            'day' => $this['day'] ? $this['day']['name_' . $lang] : '',
            "open_time" => date_format(date_create($this['open_time']), 'G:i a'),
            "close_time" => date_format(date_create($this['close_time']), 'G:i a'),
            "is_vacation" => $this['is_open'],
        ];
    }
}
