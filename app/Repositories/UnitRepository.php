<?php


namespace App\Repositories;


use App\Enums\Stock\ATransactionTypes;
use App\Events\Inventory\StockMovement;
use App\Http\Controllers\Controller;
use App\Lib\Log\ServerError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BarcodeProduct;
use App\Models\Bundle;
use App\Models\Color;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\ProductConsumerPrice;
use App\Models\ProductImage;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\Store;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitRepository extends Controller
{

    protected $model;

    public function __construct(Unit $model)
    {
        $this->model = $model;
    }

    public function getColorsForSelection()
    {
        return $this->model->select('id','name_ar as name')->get();
    }

}
