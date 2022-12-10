<?php

namespace App\Http\Controllers\Dashboard\Places;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Places\DeleteRequest;
use App\Http\Requests\Places\Areas\CreateAreasRequest;
use App\Http\Requests\Places\Areas\UpdateAreasRequest;
use App\Lib\Log\ServerError;
use App\Models\Area;
use App\Models\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AreasController extends BaseController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $query = Area::query();
            if ($request->has('city_id') && $request->city_id != '') {
                $query->where('city_id', intval($request->city_id));
            }
            $areas = $query->orderByRaw('areas.updated_at DESC NULLS LAST')->get();

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $areas

            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard areas ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateAreasRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateAreasRequest $request)
    {
        try {
            $city = new Area;
            $city->name_ar = $request->name_ar;
            $city->name_en = $request->name_en;
            $city->city_id = $request->city_id;
            $city->activation = $request->activation;
            $city->save();
            $logData['id'] = $city->id;
            $logData['ref_name_ar'] = $city->name_ar;
            $logData['ref_name_en'] = $city->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_AREA;
            event(new DashboardLogs($logData, 'areas'));
            return response()->json([
                'success' => true,
                'message' => 'Area created',
                'data' => $city
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard areas ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $city = Area::query()->find($id);

            return response()->json([
                'success' => true,
                'message' => 'Area',
                'data' => $city
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard areas ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateAreasRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAreasRequest $request)
    {
        try {
            $city = Area::query()
                ->find($request->id);
            $city->name_ar = $request->name_ar;
            $city->name_en = $request->name_en;
            $city->city_id = $request->city_id;
            $city->activation = $request->activation;

            $city->save();
            $logData['id'] = $city->id;
            $logData['ref_name_ar'] = $city->name_ar;
            $logData['ref_name_en'] = $city->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_AREA;
            event(new DashboardLogs($logData, 'areas'));
            return response()->json([
                'success' => true,
                'message' => 'Area Updated',
                'data' => $city
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard areas ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param DeleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteRequest $request)
    {
        try {
            $area = Area::query()->where('id', $request->get('id'))->first();
            Area::destroy($request->get('id'));
            $logData['id'] = $request->get('id');
            $logData['ref_name_ar'] = $area->name_ar;
            $logData['ref_name_en'] = $area->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_AREA;
            event(new DashboardLogs($logData, 'areas'));
            return response()->json([
                'success' => true,
                'message' => 'Area deleted',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard areas ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAreasForSelection(Request $request)
    {
        try {
            $query = Area::query();
            if ($request->has('city_id') && $request->city_id != '') {
                $query->where('city_id', $request->city_id);
            }
            $areas = $query->select('id', 'name_ar', 'name_en')
                ->where('activation', true)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'Areas Retrieved',
                'data' => $areas
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getAreasForSelection of dashboard areas ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
