<?php

namespace App\Http\Resources\Seller\Offers;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Http\Resources\Dashboard\Orders\UserResource;
use App\Http\Resources\Seller\Store\StoreMiniDataResource;
use App\Http\Resources\Seller\StoreResource;
use App\Models\OfferStore;
use App\Models\Product;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {

        $store = StoreRepository::getStoreByUserId($this->user_id);

        $myStore = StoreRepository::getStoreByUserId($request->user_id);

        $offerStore = OfferStore::query()->where([['offer_id', $this->id], ['store_id', $myStore->id]])->first();

        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "from" => $this->from,
            "to" => $this->to,
            "is_owner" => (bool)$this->user_id == $request->user_id,
            "activation" => (bool)$this->activation,
            'can_approve_offer' => $this->canApproveOffer($request, $offerStore),
            'status' => $offerStore ? $offerStore->status : '',
            "has_end_date" => (bool)$this->has_end_date,
            "discount_type" => DiscountTypes::getDiscountType($this->discount_type),
            'discount_type_key' => (int)$this->discount_type,
            "discount_value" => (double)$this->discount_value,
            'bulk_price' => (double)$this->bulk_price,
            'retail_price' => (double)$this->retail_price,
            'store' => new StoreMiniDataResource($store),
            "products" => $this->when($this->offers_products, OfferProductResource::collection($this->offers_products)),
            'created_at' => $this->created_at,
        ];
    }


    public function canApproveOffer($request, $offerStore): bool
    {
        if ($this->user_id == $request->user_id) {
            return false;
        }

        if ($offerStore && $offerStore->status == 'pending') {
            return true;
        }

        return false;

    }
}
