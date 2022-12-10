<?php

namespace App\Http\Resources\Seller\Categories;

use App\Http\Resources\Seller\Brands\BrandsResource;
use App\Http\Resources\Seller\Slider\SlidesResource;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoriesResource extends JsonResource
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
            'name' => $this->name,
            'category_id' => $this['category_id'],
            'image' => $this['image'],
            'categories' => SubSubCategoriesResource::collection($this['categories']),

        ];
    }
}
