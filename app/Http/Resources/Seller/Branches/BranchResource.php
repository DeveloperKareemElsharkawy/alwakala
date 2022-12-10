<?php

namespace App\Http\Resources\Seller\Branches;

use App\Enums\Orders\AOrders;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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

        $orders = Order::query()->with('products')->where('user_id', $this->user_id)->get();

        $purchasedItemCounts = $orders->pluck('items')->collapse()->pluck('purchased_item_count')->toArray();

        $storeOrderedProducts = OrderProduct::query()->where('store_id', $this->id)->get();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'logo' => $this->logo ? config('filesystems.aws_base_url') . $this->logo : null,
            'is_main_branch' => $this->is_main_branch,
            'join_date' => date_format($this->created_at, "Y-m-d H:i:s a") ?? '',
            'address_info' => [
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'building_no' => $this->building_no,
                'landmark' => $this->landmark,
                'main_street' => $this->main_street,
                'side_street' => $this->side_street,
                'city' => $this->has('city') ? new LocationResource($this->city) : null,
                'state' => $this->has('city.state') ? new LocationResource($this->city->state) : null,
                'region' => $this->has('city.state.region') ? new LocationResource($this->city->state->region) : null,
                'country' => $this->has('city.state.region.country') ? new LocationResource($this->city->state->region->country) : null,
            ],
            'branch_counters' => [
                'products_count' => count($this->productsForFeedsV2),
                'purchase_count' => array_sum($purchasedItemCounts),
                'returned_count' => array_sum($storeOrderedProducts->where('status_id', AOrders::RETURNED)->pluck('purchased_item_count')->toArray()),
                'sales_count' => array_sum($storeOrderedProducts->pluck('purchased_item_count')->toArray()),
            ],
        ];
    }
}
