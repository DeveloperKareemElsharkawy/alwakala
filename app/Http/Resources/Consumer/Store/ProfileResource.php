<?php

namespace App\Http\Resources\Consumer\Store;

use App\Http\Resources\Consumer\Order\Relations\CityResource;
use App\Http\Resources\Seller\Locations\StateResource;
use App\Http\Resources\Seller\Store\SellerRateResource;
use App\Http\Resources\Seller\Store\StoreOpeningHoursResource;
use App\Lib\Helpers\Rate\RateHelper;
use App\Models\FollowedStore;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'id' => $this->id,
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
            'address' => [
                'address' => $this->address,
                'landing_number' => $this->landing_number,
                'building_no' => $this->building_no,
                'landmark' => $this->landmark,
                'main_street' => $this->main_street,
                'side_street' => $this->side_street,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,

                'city' => new CityResource($this->city),
                'state' => new StateResource($this->city?->state),
                'country' => new CityResource($this->city?->state?->region?->country),
            ],

            'statistics' => [
                'products_count' => $this->products_count,
                'average_rate' => RateHelper::getStoreAvgRating($this->id),
            ],

            'reviews' =>  SellerRateResource::collection($this->SellerRate),
            'working_days' => StoreOpeningHoursResource::collection($this->storeOpeningHours),
        ];
    }

    public function isFollowing(): bool
    {
        return (bool)FollowedStore::query()
            ->where([['user_id', request()->user('api')?->id], ['store_id', $this->id]])->first();
    }

}
