<?php

namespace App\Http\Controllers\Dashboard\Places;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Places\DeleteRequest;
use App\Http\Requests\Places\Zones\CreateZonesRequest;
use App\Http\Requests\Places\Zones\UpdateZonesRequest;
use App\Lib\Log\ServerError;
use App\Models\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ZonesController extends BaseController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $query = Zone::query();
            if ($request->has('area_id') && $request->area_id != '') {
                $query->where('area_id', intval($request->area_id));
            }
            $zones = $query->orderByRaw('zones.updated_at DESC NULLS LAST')->get();

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $zones

            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard zones ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateZonesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateZonesRequest $request)
    {
        try {
            $zone = new Zone;
            $zone->name_ar = $request->name_ar;
            $zone->name_en = $request->name_en;
            $zone->area_id = $request->area_id;
            $zone->activation = $request->activation;
            $zone->save();
            $logData['id'] = $zone->id;
            $logData['ref_name_ar'] = $zone->name_ar;
            $logData['ref_name_en'] = $zone->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_ZONE;
            event(new DashboardLogs($logData, 'zones'));
            return response()->json([
                'success' => true,
                'message' => 'Zone created',
                'data' => $zone
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard zones ' . __LINE__ . $e);
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
            $zone = Zone::query()->find($id);

            return response()->json([
                'success' => true,
                'message' => 'Zone',
                'data' => $zone
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard zones ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateZonesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateZonesRequest $request)
    {
        try {
            $zone = Zone::query()
                ->find($request->id);
            $zone->name_ar = $request->name_ar;
            $zone->name_en = $request->name_en;
            $zone->area_id = $request->area_id;
            $zone->activation = $request->activation;

            $zone->save();
            $logData['id'] = $zone->id;
            $logData['ref_name_ar'] = $zone->name_ar;
            $logData['ref_name_en'] = $zone->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_ZONE;
            event(new DashboardLogs($logData, 'zones'));
            return response()->json([
                'success' => true,
                'message' => 'Zone Updated',
                'data' => $zone
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard zones ' . __LINE__ . $e);
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
            $state=Zone::query()->where('id', $request->get('id'))->first();
            Zone::destroy($request->get('id'));
            $logData['id'] = $request->get('id');
            $logData['ref_name_ar'] = $state->name_ar;
            $logData['ref_name_en'] = $state->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_ZONE;
            event(new DashboardLogs($logData, 'zones'));
            return response()->json([
                'success' => true,
                'message' => 'Zone deleted',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard zones ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function getZonesForSelection(Request $request)
    {
        try {
            $query = Zone::query();
            if ($request->has('area_id') && $request->area_id != '') {
                $query->where('area_id', $request->area_id);
            }
            $zones = $query->select('id', 'name_ar', 'name_en')
                ->where('activation', true)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'zones Retrieved',
                'data' => $zones
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getZonesForSelection of dashboard zones ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
