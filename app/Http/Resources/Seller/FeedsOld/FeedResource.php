<?php

namespace App\Http\Resources\Seller\FeedsOld;

use App\Events\Store\VisitStore;
use App\Lib\Helpers\Views\ViewsHelper;
use App\Models\FollowedStore;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        event(new VisitStore($request, $request->user_id, $this->id));

        return [
            'store_id' => $this->id,
            'store_type_id' => $this->store_type_id,
            'store_name' => $this->name,
            'logo' => $this->logo ? config('filesystems.aws_base_url') . $this->logo : null,
            'store_created_at' => date_format($this->created_at, "Y-m-d") ?? '',
            'views' => ViewsHelper::getViewsCount($this->id, Store::class),
            'is_followed' => (bool)FollowedStore::where([['user_id', auth('api')->user()->id ?? 0], ['store_id', $this->id]])->first(),
            'products' => ProductsFeedsResource::collection($this->productsForFeedsV2),
        ];
    }
}
