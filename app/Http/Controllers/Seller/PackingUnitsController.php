<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Models\PackingUnit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PackingUnitsController extends BaseController
{
    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function getPackingUnits()
    {
        try {
            $packingUnits = PackingUnit::query()
                ->select('id', 'name_' . $this->lang . ' as name')
                ->where('id', '!=', 1)
                ->get();
            return response()->json([
                'status' => true,
                'message' => trans('messages.inventory.packing_units'),
                'data' => $packingUnits
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getPackingUnits of seller PackingUnits' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
