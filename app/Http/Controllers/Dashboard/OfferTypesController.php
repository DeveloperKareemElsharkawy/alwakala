<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Models\OfferType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfferTypesController extends BaseController
{
    public function getForSelection()
    {
        try {

            $offerTypes = OfferType::query()
                ->select('id', 'name')
                ->get();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "offer types retrieved successfully",
                "data" => $offerTypes
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in get for selection of dashboard offers' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
