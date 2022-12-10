<?php

namespace App\Services\Sizes;

use App\Repositories\MaterialsRepository;
use App\Repositories\SizesRepository;

class SizesService
{
    private $sizesRepository;

    public function __construct(SizesRepository $sizesRepository)
    {
        $this->sizesRepository = $sizesRepository;
    }

    public function getSizesForSelection()
    {
        return $this->sizesRepository->getSizesForSelection();
    }

}
