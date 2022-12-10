<?php


namespace App\Repositories;

use App\Models\Material;
use App\Models\Size;

class SizesRepository
{

    protected $model;

    public function __construct(Size $model)
    {
        $this->model = $model;
    }

    public function getSizesForSelection()
    {
        return $this->model->newQuery()
            ->select('id', 'size')
            ->get();
    }


}
