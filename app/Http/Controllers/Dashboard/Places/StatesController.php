<?php

namespace App\Http\Controllers\Dashboard\Places;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\StatesExport;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Places\DeleteRequest;
use App\Http\Requests\Places\States\CreateStatesRequest;
use App\Http\Requests\Places\States\UpdateStatesRequest;
use App\Lib\Log\ServerError;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class StatesController extends BaseController
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
            $query = State::query()->orderByRaw('states.updated_at DESC NULLS LAST')

                //                ->orderBy('states.updated_at', 'desc')
                ->select('states.id',
                    'states.name_en',
                    'states.name_ar',
                    'states.region_id',
                    'states.activation',
                    'regions.name_en as region_name_en',
                    'regions.name_ar as region_name_ar'
                )
                ->join('regions', 'regions.id', '=', 'states.region_id');
            /*  if ($request->filled("name_ar")) {
                  $searchQuery = "%" . $request->get("name_ar") . "%";
                  $query->where('states.name_ar', "ilike", $searchQuery);
              }
              if ($request->filled("name_en")) {
                  $searchQuery = "%" . $request->get("name_en") . "%";
                  $query->where('states.name_en', "ilike", $searchQuery);
              }*/
            if ($request->filled("name")) {
                $searchQuery = "%" . $request->get("name") . "%";
                $query->where('states.name_ar', "ilike", $searchQuery)
                    ->orWhere('states.name_en', "ilike", $searchQuery);
            }
            if ($request->filled('activation')) {
                $query->where('states.activation', $request->activation);
            }
            if ($request->filled('region')) {
                $query->where('states.region_id', intval($request->region));
            }
            if ($request->filled('id')) {
                $query->where('states.id', intval($request->id));
            }
            if ($request->filled("sort_by_name_ar")) {

                $query->orderBy('states.name_ar', $request->sort_by_name_ar);
            }
            if ($request->filled("sort_by_name_en")) {

                $query->orderBy('states.name_en', $request->sort_by_name_en);
            }
            if ($request->filled('sort_by_activation')) {
                $query->orderBy('states.activation', $request->sort_by_activation);
            }
            if ($request->filled('sort_by_region')) {
                $query->orderBy('states.region_id', $request->sort_by_region);
            }
            if ($request->filled('sort_by_id')) {
                $query->orderBy('states.id', $request->sort_by_id);
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
            Log::error('error in index of dashboard status ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateStatesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateStatesRequest $request)
    {
        try {
            $state = new State;
            $state->name_ar = $request->name_ar;
            $state->name_en = $request->name_en;
            $state->region_id = $request->region_id;
            $state->activation = $request->activation;
            $state->save();
            $logData['id'] = $state->id;
            $logData['ref_name_ar'] = $state->name_ar;
            $logData['ref_name_en'] = $state->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_STATE;
            event(new DashboardLogs($logData, 'states'));
            return response()->json([
                'success' => true,
                'message' => 'State created',
                'data' => $state
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard status ' . __LINE__ . $e);
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
            $state = State::query()
                ->with('region:id,name_en,name_ar')
                ->find($id);

            return response()->json([
                'success' => true,
                'message' => 'State',
                'data' => $state
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard status ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateStatesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateStatesRequest $request)
    {
        try {
            $state = State::query()
                ->find($request->id);
            $state->name_ar = $request->name_ar;
            $state->name_en = $request->name_en;
            $state->region_id = $request->region_id;
            $state->activation = $request->activation;

            $state->save();
            $logData['id'] = $state->id;
            $logData['ref_name_ar'] = $state->name_ar;
            $logData['ref_name_en'] = $state->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_STATE;
            event(new DashboardLogs($logData, 'states'));
            return response()->json([
                'success' => true,
                'message' => 'State Updated',
                'data' => $state
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard status ' . __LINE__ . $e);
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
            $containCities = City::query()
                ->where('state_id', $request['id'])
                ->first();
            if ($containCities) {
                return response()->json([
                    'success' => false,
                    'message' => 'this state contain cities delete them first',
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $state = State::query()->where('id', $request->get('id'))->first();
            State::destroy($request->get('id'));
            $logData['id'] = $request->get('id');
            $logData['ref_name_ar'] = $state->name_ar;
            $logData['ref_name_en'] = $state->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_STATE;
            event(new DashboardLogs($logData, 'states'));
            return response()->json([
                'success' => true,
                'message' => 'State deleted',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard status ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatesForSelection(Request $request)
    {
        try {
            $query = State::query();
            if ($request->filled('region_id')) {
                $query->where('region_id', $request->region_id);
            }
            $states = $query->select('id', 'name_en', 'name_ar')
                ->where('activation', true)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'States Retrieved',
                'data' => $states
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStatesForSelection of dashboard status ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new StatesExport($request), 'states.xlsx');
        } catch (\Exception $e) {
            Log::error('error in colors in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


}
