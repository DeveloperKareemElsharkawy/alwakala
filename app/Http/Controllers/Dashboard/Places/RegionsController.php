<?php

namespace App\Http\Controllers\Dashboard\Places;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\RegionsExport;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Places\DeleteRequest;
use App\Http\Requests\Places\Regions\CreateRegionsRequest;
use App\Http\Requests\Places\Regions\UpdateRegionsRequest;
use App\Lib\Log\ServerError;
use App\Models\Region;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RegionsController extends BaseController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');
            $query = Region::query()->orderByRaw('regions.updated_at DESC NULLS LAST')
                //                ->orderBy('regions.updated_at', 'desc')
                ->select('regions.id',
                    'regions.name_en',
                    'regions.name_ar',
                    'regions.country_id',
                    'regions.activation',
                    'countries.name_en as country_name_en',
                    'countries.name_ar as country_name_ar'
                )
                ->join('countries', 'countries.id', '=', 'regions.country_id');
            /*  if ($request->filled("name_ar")) {
                  $searchQuery = "%" . $request->get("name_ar") . "%";
                  $query->where('regions.name_ar', "ilike", $searchQuery);
              }
              if ($request->filled("name_en")) {
                  $searchQuery = "%" . $request->get("name_en") . "%";
                  $query->where('regions.name_en', "ilike", $searchQuery);

              }*/
            if ($request->filled("name")) {
                $searchQuery = "%" . $request->get("name") . "%";
                $query->where('regions.name_ar', "ilike", $searchQuery)
                    ->orWhere('regions.name_en', "ilike", $searchQuery);
            }
            if ($request->filled('id')) {
                $query->where('regions.id', intval($request->id));
            }
            if ($request->filled('activation')) {
                $query->where('regions.activation', $request->activation);
            }
            if ($request->filled('country')) {
                $query->where('regions.country_id', intval($request->country));
            }
            if ($request->filled("sort_by_name_ar")) {
                $query->orderBy('regions.name_ar', $request->sort_by_name_ar);
            }
            if ($request->filled("sort_by_name_en")) {
                $query->orderBy('regions.name_en', $request->sort_by_name_en);

            }
            if ($request->filled('sort_by_id')) {
                $query->orderBy('regions.id', $request->sort_by_id);
            }
            if ($request->filled('sort_by_activation')) {
                $query->orderBy('regions.activation', $request->sort_by_activation);
            }
            if ($request->filled('sort_by_country')) {
                $query->orderBy('regions.country_id', $request->sort_by_country);
            }
            $count = $query->get()->count();
            $regions = $query->offset($offset)->limit($limit)->get();
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $regions,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard regions ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateRegionsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRegionsRequest $request)
    {
        try {
            $region = new Region;
            $region->name_ar = $request->name_ar;
            $region->name_en = $request->name_en;
            $region->country_id = $request->country_id;
            $region->activation = $request->activation;
            $region->save();
            $logData['id'] = $region->id;
            $logData['ref_name_ar'] = $region->name_ar;
            $logData['ref_name_en'] = $region->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_REGION;
            event(new DashboardLogs($logData, 'regions'));
            return response()->json([
                'success' => true,
                'message' => 'Region created',
                'data' => $region
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard regions ' . __LINE__ . $e);
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
            $region = Region::query()
                ->with('country:id,name_en,name_ar')
                ->find($id);

            return response()->json([
                'success' => true,
                'message' => 'Region',
                'data' => $region
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard regions ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateRegionsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRegionsRequest $request)
    {
        try {
            $region = Region::query()
                ->find($request->id);
            $region->name_ar = $request->name_ar;
            $region->name_en = $request->name_en;
            $region->country_id = $request->country_id;
            $region->activation = $request->activation;

            $region->save();
            $logData['id'] = $region->id;
            $logData['ref_name_ar'] = $region->name_ar;
            $logData['ref_name_en'] = $region->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_REGION;
            event(new DashboardLogs($logData, 'regions'));
            return response()->json([
                'success' => true,
                'message' => 'Region Updated',
                'data' => $region
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard regions ' . __LINE__ . $e);
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
            $containStates = State::query()
                ->where('region_id', $request['id'])
                ->first();
            if ($containStates) {
                return response()->json([
                    'success' => false,
                    'message' => 'this region contain states delete them first',
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $region = Region::query()->where('id', $request->get('id'))->first();
            Region::destroy($request->get('id'));
            $logData['id'] = $request->get('id');
            $logData['ref_name_ar'] = $region->name_ar;
            $logData['ref_name_en'] = $region->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_REGION;
            event(new DashboardLogs($logData, 'regions'));
            return response()->json([
                'success' => true,
                'message' => 'Region deleted',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard regions ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegionsForSelection(Request $request)
    {
        try {
            $query = Region::query();
            if ($request->filled('country')) {
                $query->where('country_id', $request->country_id);
            }
            $regions = $query->select('id', 'name_en', 'name_ar')
                ->where('activation', true)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'Regions Retrieved',
                'data' => $regions
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getRegionsForSelection of dashboard regions ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new RegionsExport($request), 'regions.xlsx');
        } catch (\Exception $e) {
            Log::error('error in colors in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
