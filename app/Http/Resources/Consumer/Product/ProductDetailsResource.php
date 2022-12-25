<?php

namespace App\Http\Resources\Consumer\Product;

use App\Http\Resources\Consumer\Product\Relations\ProductBrandResource;
use App\Http\Resources\Consumer\Product\Relations\ProductCategoryResource;
use App\Http\Resources\Consumer\Product\Relations\ProductImagesResource;
use App\Http\Resources\Consumer\Product\Relations\ProductMaterialResource;
use App\Http\Resources\Consumer\Product\Relations\ProductPolicyResource;
use App\Http\Resources\Consumer\Product\Relations\ProductShippingResource;
use App\Lib\Helpers\Favorite\FeedFavoriteHelper;
use App\Lib\Helpers\StoreId\StoreId;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $storeId = StoreId::getStoreID($request);
        $userID = $request->user('api') ? $request->user('api')->id : 0;
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "youtube_link" => $this->youtube_link,
            'is_favorite' => FeedFavoriteHelper::isFavorite($userID, $this['id'], $storeId),

            'images' => ProductImagesResource::collection($this->images),
            'policy' => new ProductPolicyResource($this->policy),
            'category' => new ProductCategoryResource($this->category),
            'brand' => new ProductBrandResource($this->brand),
            'material' => new ProductMaterialResource($this->material),
            'shipping' => new ProductShippingResource($this->shipping),

            
        ];
    }
}
