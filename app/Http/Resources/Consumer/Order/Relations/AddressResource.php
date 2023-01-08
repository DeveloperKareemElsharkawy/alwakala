<?php

namespace App\Http\Resources\Consumer\Order\Relations;

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
        $lang = LangHelper::getDefaultLang($request);

        return [
            "id" => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'building_no' => $this->building_no,
            'landmark' => $this->landmark,
            'main_street' => $this->main_street,
            'side_street' => $this->side_street,
            'mobile' => $this->mobile,
            'is_default' => $this->is_default,
            'city' => new CityResource($this->city)

         ];
    }
}
