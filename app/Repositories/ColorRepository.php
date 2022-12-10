<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\ProductStoreStock;

class ColorRepository extends Controller
{

    protected $model;

    public function __construct(Color $model)
    {
        $this->model = $model;
    }

    public function getColorsForSelection($request)
    {
        $query = $this->model->select('id', 'name_en', 'name_ar', 'hex');
        if ($request->filled('name')){
            $query->where(function ($q) use ($request){
                $q->where('name_ar', "like", "%".$request->query('name')."%")
                  ->orWhere('name_en', "like", "%".$request->query('name')."%");
            });
        }
        return $query->get();
    }

    public static function checkIfColorExists($productStore_id, $color_id)
    {
        return ProductStoreStock::query()
            ->where('product_store_id', $productStore_id)
            ->where('color_id', $color_id)
            ->first();
    }
}
