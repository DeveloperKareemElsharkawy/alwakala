<?php

namespace App\Http\Resources\Consumer\Store;

use App\Http\Resources\Consumer\Order\OrderItemsResource;
use App\Http\Resources\Consumer\Order\Relations\CityResource;
use App\Http\Resources\Seller\Locations\StateResource;
use App\Http\Resources\Seller\Store\SellerRateResource;
use App\Http\Resources\Seller\Store\StoreOpeningHoursResource;
use App\Lib\Helpers\Rate\RateHelper;
use App\Models\FollowedStore;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStoreProductsResource extends JsonResource
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
//            'id' => $this['id'],
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'description' => $this->description,

            'images' => [
                'logo' => $this->logo ? config('filesystems.aws_base_url') . $this->logo : null,
                'licence' => $this->licence ? config('filesystems.aws_base_url') . $this->licence : null,
                'cover' => $this->cover ? config('filesystems.aws_base_url') . $this->cover : null,
            ],

            'qr' => [
                'image' => $this->qr_code_image ? config('filesystems.aws_base_url') . $this->qr_code_image : null,
                'code' => $this->qr_code,
            ],

            'cases' => [
                'is_store_has_delivery' => $this->is_store_has_delivery,
                'is_verified' => $this->is_verified,
                'is_following' => $this->isFollowing(),
            ],

            'contact' => [
                'owner_name' => $this->owner->name,
                'owner_email' => $this->owner->email,
                'owner_mobile' => $this->owner->mobile,
                'store_mobile' => $this->mobile,
            ],

            'items' => OrderItemsResource::collection($this['items'])
        ];
    }

    public function isFollowing(): bool
    {
        return (bool)FollowedStore::query()
            ->where([['user_id', request()->user('api')?->id], ['store_id', $this->id]])->first();
    }

}
