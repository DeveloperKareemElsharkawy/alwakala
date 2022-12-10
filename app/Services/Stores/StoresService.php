<?php

namespace App\Services\Stores;

use App\Repositories\StoreRepository;

class StoresService
{
    private $storeRepository;

    public function __construct(
        StoreRepository $storeRepository
    )
    {
        $this->storeRepository = $storeRepository;
    }

    public function getStoreRating($storeId) {
        $ratingData = $this->storeRepository->getStoreRatings($storeId)[0];
        if ($ratingData['rating'] == null) {
            return 5;
        }
        return round($ratingData['rating'] / $ratingData['reviews_count']);
    }
}
