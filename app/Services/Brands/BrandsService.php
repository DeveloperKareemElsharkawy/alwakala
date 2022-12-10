<?php

namespace App\Services\Brands;

use App\Repositories\BrandsRepository;

class BrandsService
{
    private $brandsRepository;

    public function __construct(BrandsRepository $brandsRepository)
    {
        $this->brandsRepository = $brandsRepository;
    }

    public function getBrandsForSelection($lang)
    {
       return $this->brandsRepository->getBrandsForSelection($lang);
    }

}
