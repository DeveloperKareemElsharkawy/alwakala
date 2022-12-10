<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandsRepository
{
    protected $model;

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    public function getBrandsForSelection($lang)
    {
        return $this->model->newQuery()
            ->select('id', 'name_'. $lang . ' as name')
            ->where('activation', true)
            ->get();
    }

}
