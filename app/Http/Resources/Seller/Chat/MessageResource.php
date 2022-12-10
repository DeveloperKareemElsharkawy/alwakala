<?php

namespace App\Http\Resources\Seller\Chat;

use App\Http\Resources\Seller\Orders\StoreResource;
use App\Http\Resources\Seller\Orders\UserResource;
use App\Http\Resources\Seller\ProductResource;
use App\Http\Resources\Seller\Store\StoreCardResource;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],

            'message' => $this['message'],
            'image' => $this['image'] ? config('filesystems.aws_base_url') . $this['image'] : null,
            'video' => $this['video'] ? config('filesystems.aws_base_url') . $this['video'] : null,
            'record' => $this['record'] ? config('filesystems.aws_base_url') . $this['record'] : null,

            'is_seen' => $this['is_seen'],
            'is_sender' => $this->isSender($request, $this['storeSender']),

            'sender' => [
                'user' => new UserResource($this['userSender']),
                'store' => new StoreCardResource($this['storeSender']),
            ],
            'receiver' => [
                'user' => new UserResource($this['userReceiver']),
                'store' => new StoreCardResource($this['storeReceiver']),
            ],

            'parent' => new MessageResource($this['parent']),

            'time' => $this['created_at']->format('j/m/Y g:i A')
        ];
    }


    function isSender($request, $store): bool
    {
        $storeID = StoreId::getStoreID($request);

        return $storeID == $store->id;
    }
}
