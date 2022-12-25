<?php

namespace App\Http\Resources\Consumer\Product\Relations;

use App\Lib\Helpers\Rate\RateHelper;
use App\Models\FollowedStore;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $followersCount = FollowedStore::query()->where('store_id', $this->id)->count();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'followers_count' => $followersCount,
            'rating_avg' => RateHelper::getStoreAvgRating($this->id),
            'logo' => $this->logo ? config('filesystems.aws_base_url') . $this->logo : null,
            'is_followed' => (bool)FollowedStore::where([['user_id', auth('api')?->user()?->id ], ['store_id', $this->id]])->first(),
            'is_verified' => $this->is_verified,
        ];
    }
}
