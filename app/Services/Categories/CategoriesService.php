<?php

namespace App\Services\Categories;

use App\Repositories\CategoriesRepository;

class CategoriesService
{
    private $categoriesRepository;

    public function __construct(CategoriesRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    public function getCategoriesByLevel($id, $lang, $type)
    {
        if ($id) {
            return $this->categoriesRepository->getChildes([$id], $lang);
        }
        switch ($type) {
            case 'sub-sub':
                $parentsIds = $this->categoriesRepository->getParentsIds();
                $childesIds = $this->categoriesRepository->getParentsIds($parentsIds);
                $categories = $this->categoriesRepository->getChildes($childesIds, $lang);
                break;
            case 'sub':
                $parentsIds = $this->categoriesRepository->getParentsIds();
                $categories = $this->categoriesRepository->getChildes($parentsIds, $lang);
                break;
            case 'parent':
                $categories = $this->categoriesRepository->getParents($lang);
                break;
            default:
                $categories = $this->categoriesRepository->getParents($lang);
        }
        return $categories;
    }

    public function show($id)
    {
        return $this->categoriesRepository->showById($id);
    }

}
