<?php

namespace App\Http\Resources\Dashboard\Stock;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $lang = LangHelper::getDefaultLang($request);

        return [
            "id" => $this->id,
            "name" => $this['name_' . $lang],
            'hex' => $this->hex,
        ];
    }
}
