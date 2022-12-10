<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\StoreType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class StoreTypesController extends BaseController
{
    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function getForSelection()
    {
        try {
            $store_types = StoreType::query()
                ->select('id', 'name_' . $this->lang . ' as name','description_' . $this->lang . ' as description')
                ->get();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => trans('messages.stores.store_types_retrieved'),
                "store_types" => $store_types
            ], AResponseStatusCode::SUCCESS);
        }
        catch (\Exception $e){
            Log::error('error in getForSelection of seller StoreTypes' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }
}
