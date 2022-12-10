<?php

namespace App\Http\Resources\Seller\Offers;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Models\Offer;
use App\Models\ProductStore;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferProductResource extends JsonResource
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
            "product" => $this->when($this->product, new ProductResource($this->product)),
            'price_from_my_store_inventory' => OffersHelper::getPriceFromStoreInventory($request, $this->offer_id, $this->product_id)
        ];
    }

}
