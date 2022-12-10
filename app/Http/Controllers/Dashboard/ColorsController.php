<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\ColorsExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Dashboard\Color\StoreColorRequest;
use App\Http\Requests\Dashboard\Color\UpdateColorRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Color;
use App\Models\ProductStoreStock;
use App\Repositories\ColorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;

class ColorsController extends BaseController
{
    public $colorsRepo;
    private $lang;

    public function __construct(ColorRepository $colorsRepository, Request $request)
    {
        $this->colorsRepo = $colorsRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function index(Request $request)
    {
        try {
            $query = Color::query()
                ->orderByRaw('colors.updated_at DESC NULLS LAST');
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');

            if ($request->filled('name')) {
                $query->where('name_ar', 'ilike', '%' . $request->query('name') . '%')
                    ->orWhere('name_en', 'ilike', '%' . $request->query('name') . '%');
            }
            if ($request->filled('sort_by_name_en')) {
                $query->orderBy('name_en', $request->query('sort_by_name_en'));
            }
            if ($request->filled('sort_by_name_ar')) {
                $query->orderBy('name_ar', $request->query('sort_by_name_ar'));
            }
            if ($request->filled('hex')) {
                $query->where('hex', 'ilike', '%' . $request->query('hex') . '%');
            }
            if ($request->filled('sort_by_id')) {
                $query->orderBy('id', $request->query('sort_by_id'));
            }
            $count = $query->count();
            $colors = $query->offset($offset)->limit($limit)->get();

            return response()->json([
                "status" => true,
                "message" => "colors",
                "data" => $colors,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard colors' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getForSelection(Request $request)
    {
        try {
            $data = $this->colorsRepo->getColorsForSelection($request);
            return response()->json([
                "status" => true,
                "message" => "colors retrieved successfully",
                "data" => $data
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getForSelection of seller colors' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function store(StoreColorRequest $request)
    {
        try {
            $color = new Color;
            $color->fill($request->validated());
            $color->save();

            return response()->json([
                "status" => true,
                "message" => "color created",
                "data" => $color
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard colors' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function show($id)
    {
        try {
            $color = Color::query()->where('id', $id)->first();
            if (!$color) {
                return response()->json([
                    'status' => false,
                    'message' => 'color not found',
                    'data' => ''
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                "status" => true,
                "message" => "show color",
                "data" => $color
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard colors' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function update(UpdateColorRequest $request)
    {
        try {
            $color = Color::query()->where('id', $request->id)->first();
            $color->name_ar = $request->name_ar;
            $color->name_en = $request->name_en;
            $color->hex = $request->hex;
            $color->save();

            return response()->json([
                "status" => true,
                "message" => "color updated",
                "data" => $color
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard colors' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function delete($id)
    {
        try {
            $color = Color::query()->where('id', $id)->first();
            if (!$color) {
                return response()->json([
                    'status' => false,
                    'message' => 'color not found',
                    'data' => ''
                ], Response::HTTP_NOT_FOUND);
            }
            $colorExists = ProductStoreStock::query()->where('color_id', $id)->first();
            if ($colorExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'cant delete color while its related to products',
                    'data' => ''
                ], Response::HTTP_NOT_ACCEPTABLE);
            }
            $color->delete();

            return response()->json([
                "status" => true,
                "message" => "color deleted",
                "data" => ''
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard colors' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new ColorsExport($request), 'colors.xlsx');
        } catch (\Exception $e) {
            Log::error('error in colors in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
