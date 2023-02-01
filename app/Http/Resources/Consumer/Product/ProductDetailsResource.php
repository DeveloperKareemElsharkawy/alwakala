<?php

namespace App\Http\Resources\Consumer\Product;

use App\Http\Resources\Consumer\Product\Relations\ProductBrandResource;
use App\Http\Resources\Consumer\Product\Relations\ProductCategoryResource;
use App\Http\Resources\Consumer\Product\Relations\ProductImagesResource;
use App\Http\Resources\Consumer\Product\Relations\ProductMaterialResource;
use App\Http\Resources\Consumer\Product\Relations\ProductPolicyResource;
use App\Http\Resources\Consumer\Product\Relations\ProductShippingResource;
use App\Http\Resources\Consumer\Product\Relations\ProductStoreResource;
use App\Http\Resources\Seller\Coupons\CouponDiscountsResource;
use App\Http\Resources\Seller\Store\SellerRateResource;
use App\Lib\Helpers\Favorite\FeedFavoriteHelper;
use App\Lib\Helpers\Product\ProductVariationHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use App\Services\Product\ProductService;
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
        $shareCode = UserId::GetShareCodeByUserId($userID);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'youtube_link' => $this->youtube_link,
            'is_favorite' => FeedFavoriteHelper::isFavorite($userID, $this['id'], $storeId),
            'rating_avg' => RateHelper::getProductAvgRating($this?->productStore?->store_id, $this->id),
            'reviews' => SellerRateResource::collection(RateHelper::getProductReviews($this?->productStore?->store_id, $this->id)),
            'size_table_image' => $this->size_table_image ? config('filesystems.aws_base_url') . $this->size_table_image : null,
            'has_share_coupon' => (bool)$this->productStore->shareCoupon,
            'share_coupon' => $this->shareCoupon($shareCode),

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
            'policy' => new ProductPolicyResource($this->policy),
            'category' => new ProductCategoryResource($this->category),
            'brand' => new ProductBrandResource($this->brand),
            'material' => new ProductMaterialResource($this->material),
            'shipping' => new ProductShippingResource($this->shipping),

        ];
    }

    public function shareCoupon($shareCode)
    {
        $coupon = $this->productStore?->shareCoupon?->coupon;

        if ($coupon) {
            $couponCode = $shareCode ? $coupon->code . '_share_from_' . $shareCode : null;
            $couponDiscounts = CouponDiscountsResource::collection($this->productStore->shareCoupon->coupon->discounts);
        }

        return ['is_available' => $couponCode ?? null, 'coupon_code' => $couponCode ?? null, 'discount' => $couponDiscounts ?? []];

    }

}
