<?php

namespace App\Http\Resources\Seller\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreProfileResource extends JsonResource
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
            "id" => $this['id'],
            "name" => $this['name'],
            "mobile" => $this['mobile'],
            "logo" => $this['logo'],
            "cover" => $this['cover'],
            "description" => $this['description'],
            "store_images" => $this['storeImages'],
            "seller_rate" => SellerRateResource::collection($this['sellerRate']),

            "is_follow" => $this['is_follow'],
            "store_type_id" => $this['store_type_id'],
            "is_store_has_delivery" => $this['is_store_has_delivery'],
            "user_id" => $this['user_id'],

            'legal_info' => [
                "licence" => $this['licence'],
                "legal_name" => $this['legal_name'],
                "is_verified_logo" => $this['is_verified_logo'],
                "is_verified_cover" => $this['is_verified_cover'],
                "is_verified_licence" => $this['is_verified_licence'],
                "activation" => $this['activation'],
                "is_verified" => $this['is_verified'],
            ],

            'counters' => [
                "rate" => $this['rate'],
                "comments" => $this['comments'],
                "following" => $this['following'],
            ],

            'qr_code' => [
                "code" => $this['qr_code'],
                "image" => $this['qr_code_image'],
            ],
            'address' => [
                "address" => $this['address'],
                "landing_number" => $this['landing_number'],
                "latitude" => $this['latitude'],
                "longitude" => $this['longitude'],
                "building_no" => $this['building_no'],
                "landmark" => $this['landmark'],
                "main_street" => $this['main_street'],
                "side_street" => $this['side_street'],
            ],

            "city" => [
                "id" => $this['city_id'],
                "name" => $this['city_name'],
            ],
            "state" => [
                "id" => $this['state_id'],
                "name" => $this['state_name'],
            ],
            "country" => [
                "id" => $this['country_id'],
                "name" => $this['country_name'],
            ],

            'working_days' => $this['working_days'],

            'social' => [
                "website" => $this['website'],
                "facebook" => $this['facebook'],
                "instagram" => $this['instagram'],
                "whatsapp" => $this['whatsapp'],
                "twitter" => $this['twitter'],
                "pinterest" => $this['pinterest'],
                "youtube" => $this['youtube'],
            ],

        ];
    }
}
