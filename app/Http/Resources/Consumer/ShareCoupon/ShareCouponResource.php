<?php

namespace App\Http\Resources\Consumer\ShareCoupon;

use App\Enums\DiscountTypes\DiscountTypes;
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
use App\Models\User;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShareCouponResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $user = User::query()->where('id', $request['user_id'])->first();

        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'email' => $this['email'],
            'mobile' => $this['mobile'],
            'image' => $this->image ? config('filesystems.aws_base_url') . $this->image : null,
            'is_main_participant' => (bool) $this->share_coupon_code  == $user->share_coupon_code,
            'products' => $this->products,
            'total_price' => $this->total_price,
            'discount_type' => $this->discount_type,
            'discount' => (double)$this->discount,

        ];
    }


}
