<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Repositories\ColorRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Support\Facades\Log;

class ColorsController extends BaseController
{
    public $colorsRepo;
    private $lang;

    public function __construct(ColorRepository $colorsRepository, Request $request)
    {
        $this->colorsRepo = $colorsRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function getForSelection(Request $request)
    {
        try {

            $objects = Color::query()
                ->select('id', 'name_' . $this->lang.' as name', 'hex');
            if ($request->filled('name')) {
                $objects->where('name_' . $this->lang, "like", "%" . $request->query('name') . "%");
            }
            $objects = $objects->get();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => trans('messages.general.listed'),
                "data" => $objects
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getForSelection of seller colors' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }
}
