<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Material;

class MaterialsRepository
{

    protected $model;

    public function __construct(Material $model)
    {
        $this->model = $model;
    }

    public function getMaterialsForSelection($lang = 'ar')
    {
        return $this->model->newQuery()
            ->select('id', 'name_' . $lang . ' as name')
            ->get();
    }


}
