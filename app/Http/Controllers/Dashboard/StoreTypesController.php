<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Models\StoreType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class StoreTypesController extends BaseController
{
    public function getForSelection()
    {
        try {
            $store_types = StoreType::query()
                ->select('id', 'name_en as name')
                ->get();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "store types retrieved successfully",
                "store_types" => $store_types
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStoresForSelection of dashboard StoreTypes' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }
}
