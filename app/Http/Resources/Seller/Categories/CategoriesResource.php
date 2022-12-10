<?php

namespace App\Http\Resources\Seller\Categories;

use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
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
            'name' => $this['name_' . $lang],
            'image' => config('filesystems.aws_base_url') . $this['image'],
            'category_id' => $this['category_id'],
            'has_sub' => (bool)count($this['categories']),
        ];
    }
}
