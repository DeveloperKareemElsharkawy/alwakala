<?php

namespace App\Http\Resources\Seller\Faq;

use App\Http\Resources\Seller\ProductResource;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
            'question' => $this['question_' . $lang],
            'answer' => $this['answer_' . $lang],
         ];
    }
}
