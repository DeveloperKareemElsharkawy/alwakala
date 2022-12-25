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
            'followers_count' => $followersCount,
            'rating' => RateHelper::getStoreAvgRating($this->id),
            'logo' => $this->logo ? config('filesystems.aws_base_url') . $this->logo : null,
        ];
    }
}
