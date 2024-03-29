<?php

namespace App\Http\Resources\Consumer\Product;

use App\Http\Resources\Consumer\Product\Relations\ProductBrandResource;
use App\Http\Resources\Consumer\Product\Relations\ProductCategoryResource;
use App\Http\Resources\Consumer\Product\Relations\ProductImagesResource;
use App\Http\Resources\Consumer\Product\Relations\ProductMaterialResource;
use App\Http\Resources\Consumer\Product\Relations\ProductPolicyResource;
use App\Http\Resources\Consumer\Product\Relations\ProductShippingResource;
use App\Http\Resources\Consumer\Product\Relations\ProductStoreResource;
use App\Lib\Helpers\Favorite\FeedFavoriteHelper;
use App\Lib\Helpers\Product\ProductVariationHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $userID = $request->user('api') ? $request->user('api')->id : 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'is_favorite' => FeedFavoriteHelper::isFavorite($userID, $this['id']),
            'rating_avg' => RateHelper::getProductAvgRating($this?->productStore?->store_id, $this->id),

            'pricing' => [
                'price' => (double)$this->productStore->consumer_price,
                'is_free_shipping' => (bool)$this->productStore->free_shipping,
                'has_discount' => (bool)$this->productStore->consumer_price_discount,
                'old_price' => (double)$this->productStore->consumer_old_price,
                'discount' => (double)$this->productStore->consumer_price_discount,
                'discount_type' => (double)$this->productStore->consumer_price_discount_type,
            ],

            'images' => ProductImagesResource::collection($this->images),
            'options' => ProductVariationHelper::getProductVariationsForSelection($this->productStore?->productStoreStock),
            'store' => new ProductStoreResource($this->productStore->store),
            'category' => new ProductCategoryResource($this->category),
            'brand' => new ProductBrandResource($this->brand),
            'material' => new ProductMaterialResource($this->material),

        ];
    }


}
