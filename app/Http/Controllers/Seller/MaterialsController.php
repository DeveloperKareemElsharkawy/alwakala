<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Material;
use App\Repositories\MaterialRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaterialsController extends BaseController
{
    public function getMaterials(Request $request)
    {
        try {
            $lang = LangHelper::getDefaultLang($request);
            $materials = Material::query()
                ->select('id', 'name_' . $lang . ' as name')
                ->get();
            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => $materials
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in list materials' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
