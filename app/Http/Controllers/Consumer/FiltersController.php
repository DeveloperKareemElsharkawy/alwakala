<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Services\Brands\BrandsService;
use App\Services\Categories\CategoriesService;
use App\Services\Colors\ColorsService;
use App\Services\Materials\MaterialsService;
use App\Services\Sizes\SizesService;
use Illuminate\Http\Request;

class FiltersController extends BaseController
{

    public $categoriesService;
    public $brandsService;
    public $materialsService;
    public $sizesService;
    public $colorsService;
    private $lang;

    public function __construct(Request           $request,
                                CategoriesService $categoriesService,
                                MaterialsService  $materialsService,
                                SizesService      $sizesService,
                                ColorsService     $colorsService,
                                BrandsService     $brandsService)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->categoriesService = $categoriesService;
        $this->brandsService = $brandsService;
        $this->materialsService = $materialsService;
        $this->sizesService = $sizesService;
        $this->colorsService = $colorsService;
    }

    public function getFilters()
    {
        $response = [
            'categories' => $this->categoriesService->getCategoriesByLevel(null, $this->lang, 'sub-sub'),
            'brands' => $this->brandsService->getBrandsForSelection($this->lang),
            'materials' => $this->materialsService->getMaterialsForSelection($this->lang),
            'sizes' => $this->sizesService->getSizesForSelection(),
            'colors' => $this->colorsService->getColorsForSelection($this->lang),
            'services' => [
                'Free Shipping'
            ],
        ];

        return $this->success([
            'message' => trans('messages.general.listed'),
            'data' => $response
        ]);
    }

}
