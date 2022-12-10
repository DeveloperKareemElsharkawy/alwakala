<?php

namespace App\Services\Materials;

use App\Repositories\MaterialsRepository;

class MaterialsService
{
    private $materialsRepository;

    public function __construct(MaterialsRepository $materialsRepository)
    {
        $this->materialsRepository = $materialsRepository;
    }

    public function getMaterialsForSelection($lang)
    {
        return $this->materialsRepository->getMaterialsForSelection($lang);
    }

}
