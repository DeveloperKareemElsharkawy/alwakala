<?php

namespace App\Services\Colors;

use App\Repositories\ColorRepository;

class ColorsService
{
    private $colorRepository;

    public function __construct(ColorRepository $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function getColorsForSelection($lang)
    {
        return $this->colorRepository->getColorsForSelection($lang);
    }

}
