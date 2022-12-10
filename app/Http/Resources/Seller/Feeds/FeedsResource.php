<?php

namespace App\Http\Resources\Seller\Feeds;

use App\Enums\Apps\AApps;
use App\Events\Feed\VisitFeed;
use App\Events\Product\VisitProduct;
use App\Events\Store\VisitStore;
use App\Lib\Helpers\Favorite\FeedFavoriteHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
 use App\Lib\Helpers\Views\ViewsHelper;
use App\Models\Feed;
use App\Models\FollowedStore;
use App\Models\Product;
use App\Models\Store;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request): array
    {
        event(new VisitFeed($request, $request->user_id, $this->id));

        $storeId = StoreId::getStoreID($request);
        $userID = $request->user('api') ? $request->user('api')->id : 0;

        return [
            'id' => $this['id'],
            'video' => $this['youtube_url'],
            'is_images' => (bool)$this['images'],
            'is_favorite' => FeedFavoriteHelper::isFavorite($userID, $this['id'], $storeId),
            'views' => ViewsHelper::getViewsCount($this->id, Feed::class),
            'images' => (bool)$this['images'] ? $this->imagesResponse($this['images']) : null,
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'logo' => $this->store->logo ? config('filesystems.aws_base_url') . $this->logo : null,
                'is_following' => (bool)FollowedStore::query()
                    ->where([['user_id', \request()->user_id ?? 0], ['store_id', $this->store->id]])->first(),

            ],
            'products' => ProductsFeedsResource::collection($this['products']),
            'time' => $this['created_at']->format('d-m-Y h:i A')
//            'store_type_id' => $this->store_type_id,
//            'store_created_at' => date_format($this->created_at, "Y-m-d") ?? '',
//            'views' => ViewsHelper::getViewsCount($this->id, Store::class),
        ];
    }

    public function imagesResponse($images): array
    {
        $imagesList = [];
        foreach ($images as $image) {
            $imagesList [] = config('filesystems.aws_base_url') . $image;
        }
        return $imagesList;
    }

}
