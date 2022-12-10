<?php

namespace App\Http\Resources\Seller\Categories;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesIndexResource extends JsonResource
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
            'id' => $this['id'],
            'name' => $this['name'],
            'label_title' => $this['label_title_' . $lang] ? $this['label_title_' . $lang] : trans('messages.general.size'),
            'category_id' => $this['category_id'],
            'has_sub' => (bool)count($this['categories']),
        ];
    }
}
