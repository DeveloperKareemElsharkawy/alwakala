<?php

namespace App\Http\Resources\Seller\Locations;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'street_name' => $this['street_name'],
            'latitude' => $this['latitude'],
            'longitude' => $this['longitude'],
            'is_default' => $this['is_default'],
            'building_no' => $this['building_no'],
            'landmark' => $this['landmark'],
            'city' => new CityResource($this['city']),
            'state' => new StateResource($this->city->state),
        ];
    }
}
