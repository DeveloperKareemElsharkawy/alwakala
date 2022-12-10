<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Repositories\UnitRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class UnitsController extends BaseController
{
    public $unitRepo;

    public function __construct(UnitRepository $unitRepository)
    {
        $this->unitRepo = $unitRepository;
    }

    public function getForSelection()
    {
        try {
            $data = $this->unitRepo->getColorsForSelection();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => trans('messages.product.store_not_found'),
                "data" => $data
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getForSelection of seller Units' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
