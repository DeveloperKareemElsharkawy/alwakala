<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\MaterialsExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Materials\CreateMaterialRequest;
use App\Http\Requests\Materials\UpdateMaterialRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Material;
use App\Models\Product;
use App\Repositories\MaterialRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class MaterialController extends BaseController
{

    public function index(Request $request)
    {
        try {
            $Materials = MaterialRepository::showAll($request);
            return response()->json([
                'status' => true,
                'message' => 'Materials',
                'data' => $Materials
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in index of Materials index' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function store(CreateMaterialRequest $request)
    {
        try {
            $material = MaterialRepository::store($request);
            return response()->json([
                'status' => true,
                'message' => trans('messages.materials.save'),
                'data' => $material
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in index of Materials store' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function show($id)
    {
        try {
            $material = MaterialRepository::show($id);
            if (!$material) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.not_found'),
                    'data' => '',

                ], AResponseStatusCode::NOT_FOUNT);
            }
            return response()->json([
                'status' => true,
                'message' => 'show',
                'data' => $material
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in index of Materials show' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function update(UpdateMaterialRequest $request)
    {
        try {
            $material = MaterialRepository::update($request);
            return response()->json([
                'status' => true,
                'message' => trans('messages.materials.update'),
                'data' => $material
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in index of Materials update' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::query()->where('material_id', $id)->first();
            if ($product) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.materials.can_not_destroy'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $material = MaterialRepository::show($id);
            if (!$material) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.not_found'),
                    'data' => '',
                ], AResponseStatusCode::NOT_FOUNT);
            }
            MaterialRepository::destroy($id);
            return response()->json([
                'status' => true,
                'message' => trans('messages.materials.destroy'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in index of Materials destroy' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return  Excel::download(new MaterialsExport($request), 'materials.xlsx');
        } catch (\Exception $e) {
            Log::error('error in colors in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

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
