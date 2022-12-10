<?php

namespace App\Http\Resources\Seller\Chat;

use App\Enums\StoreTypes\StoreType;
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

class ConversationResource extends JsonResource
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

            'chat_with' => [
                'user' => new UserResource($this->isSender($request, $this['storeSender']) ? $this['userReceiver'] : $this['userSender']),
                'store' => new StoreCardResource($this->isSender($request, $this['storeSender']) ? $this['storeReceiver'] : $this['storeSender']),
            ],

            'message' => $this['message']['message'],
            'is_image' => (bool)$this['image'],
            'is_video' => (bool)$this['video'],
            'is_record' => (bool)$this['record'],

            'is_seen' => $this['is_seen'],
            'is_sender' => $this->isSender($request, $this['storeSender']),

            'is_supplier' => (bool)$this['storeSender']['store_type_id'] == StoreType::SUPPLIER,

            'time' => $this['message']['created_at']->format('j/m/Y g:i A')
        ];
    }


    function isSender($request, $store): bool
    {

        $storeID = StoreId::getStoreID($request);

        return $storeID == $store->id;
    }
}
