<?php

namespace App\Http\Resources\Seller\Offers;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Http\Resources\Dashboard\Orders\UserResource;
use App\Http\Resources\Seller\Store\StoreMiniDataResource;
use App\Http\Resources\Seller\StoreResource;
use App\Lib\Helpers\Lang\LangHelper;
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

        $lang = LangHelper::getDefaultLang($request);

        $store = StoreRepository::getStoreByUserId($this->user_id);

        $myStore = StoreRepository::getStoreByUserId($request->user_id);

        $offerStore = OfferStore::query()->where([['offer_id', $this->id], ['store_id', $myStore->id]])->first();

        return [
            "id" => $this->id,

            "name" => $this->{'name_'.$lang},
            "description" => $this->description,
            "image" => $this->imageUrl,

            "start_date" => $this->start_date,
            "start_time" => $this->start_time,
            "end_date" => $this->end_date,
            "end_time" => $this->end_time,

            "is_owner" => (bool)$this->user_id == $request->user_id,
            "activation" => (bool)$this->is_avtive,
            'can_approve_offer' => $this->canApproveOffer($request, $offerStore),


            "type" => $this->type,
            "target" => $this->target,


            'status' => $offerStore ? $offerStore->status : 'pending',

            'discount_type_key' => (int)$this->discount_type,
            "discount_type" => DiscountTypes::getDiscountType($this->discount_type),
            "discount_value" => (double)$this->discount_value,

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
