<?php

namespace App\Http\Resources\Seller\Chat;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MessagesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param Request $request
     *
     */

    public $collects = MessageResource::class;

    public function toArray($request): array
    {
        return ['status' => true, 'message' => trans('messages.general.listed'), 'data' => $this->collection];
    }
}
