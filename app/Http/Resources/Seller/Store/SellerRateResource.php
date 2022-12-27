<?php

namespace App\Http\Resources\Seller\Store;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerRateResource extends JsonResource
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
        return [
            "id" => $this->id,

            'rate' => [
                "rate" => (int)$this->rate,
                "review" => $this->review,
                'images' => count(json_decode($this->images)) ? $this->imagesResponse(json_decode($this->images)) : null,
            ],

            'rater' => [
                "id" => (int)$this->rater_id,
                "name" => $this->rated_by ? $this->rated_by : $this->rater->name,
                "image" => $this->rater_image ? config('filesystems.aws_base_url') . $this->rater_image : null,
            ],

            "created_at" => $this->created_at ? date_format(new \DateTime($this->created_at), 'd-m-y G:i a') : null,

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
