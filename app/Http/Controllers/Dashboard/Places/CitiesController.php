<?php

namespace App\Http\Controllers\Dashboard\Places;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\CitiesExport;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Places\DeleteRequest;
use App\Http\Requests\Places\Cities\CreateCitiesRequest;
use App\Http\Requests\Places\Cities\UpdateCitiesRequest;
use App\Lib\Log\ServerError;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CitiesController extends BaseController
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
            $query = City::query()
//                ->orderBy('cities.updated_at', 'desc')
                ->select('cities.id',
                    'cities.name_en',
                    'cities.name_ar',
                    'cities.state_id',
                    'cities.activation',
                    'states.name_en as state_name_en',
                    'states.name_ar as state_name_ar',
                    'cities.created_at',
                    'cities.updated_at'

                )
                ->join('states', 'states.id', '=', 'cities.state_id');
            /* if ($request->filled("name_ar")) {
                 $searchQuery = "%" . $request->get("name_er") . "%";
                 $query->where('cities.name_ar', "ilike", $searchQuery);
             }
             if ($request->filled("name_en")) {
                 $searchQuery = "%" . $request->get("name_en") . "%";
                 $query->where('cities.name_en', "ilike", $searchQuery);
             }*/
            if ($request->filled("name")) {
                $searchQuery = "%" . $request->get("name") . "%";
                $query->where('cities.name_ar', "ilike", $searchQuery)
                    ->orWhere('cities.name_en', "ilike", $searchQuery);
            }
            if ($request->filled('activation')) {
                $query->where('cities.activation', $request->activation);
            }
            if ($request->filled('state')) {
                $query->where('cities.state_id', intval($request->state));
            }
            if ($request->filled('id')) {
                $query->where('cities.id', intval($request->id));
            }
            if ($request->filled("sort_by_name_ar")) {
                $query->orderBy('cities.name_ar', $request->sort_by_name_ar);
            }
            if ($request->filled("sort_by_name_en")) {
                $query->orderBy('cities.name_en', $request->sort_by_name_en);
            }
            if ($request->filled('sort_by_activation')) {
                $query->orderBy('cities.activation', $request->sort_by_activation);
            }
            if ($request->filled('sort_by_state')) {
                $query->orderBy('cities.state_id', $request->sort_by_state);
            }
            if ($request->filled('sort_by_id')) {
                $query->orderBy('cities.id', $request->sort_by_id);
            }
            $count = $query->get()->count();
            $regions = $query->orderByRaw('cities.updated_at DESC NULLS LAST')->offset($offset)->limit($limit)->get();
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $regions,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard cities ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateCitiesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCitiesRequest $request)
    {
        try {
            $city = new City;
            $city->name_ar = $request->name_ar;
            $city->name_en = $request->name_en;
            $city->state_id = $request->state_id;
            $city->activation = $request->activation;
            $city->save();
            $logData['id'] = $city->id;
            $logData['ref_name_ar'] = $city->name_ar;
            $logData['ref_name_en'] = $city->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_CITY;
            event(new DashboardLogs($logData, 'cities'));
            return response()->json([
                'success' => true,
                'message' => 'City created',
                'data' => $city
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard cities ' . __LINE__ . $e);
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
            $city = City::query()
                ->with('state:id,name_en,name_ar')
                ->find($id);

            return response()->json([
                'success' => true,
                'message' => 'City',
                'data' => $city
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard cities ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateCitiesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCitiesRequest $request)
    {
        try {
            $city = City::query()
                ->find($request->id);
            $city->name_ar = $request->name_ar;
            $city->name_en = $request->name_en;
            $city->state_id = $request->state_id;
            $city->activation = $request->activation;

            $city->save();
            $logData['id'] = $city->id;
            $logData['ref_name_ar'] = $city->name_ar;
            $logData['ref_name_en'] = $city->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_CITY;
            event(new DashboardLogs($logData, 'cities'));
            return response()->json([
                'success' => true,
                'message' => 'City Updated',
                'data' => $city
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard cities ' . __LINE__ . $e);
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
            $city = City::query()->where('id', $request->get('id'))->first();
            City::destroy($request->get('id'));
            $logData['id'] = $request->get('id');
            $logData['ref_name_ar'] = $city->name_ar;
            $logData['ref_name_en'] = $city->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_CITY;
            event(new DashboardLogs($logData, 'cities'));
            return response()->json([
                'success' => true,
                'message' => 'City deleted',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard cities ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCitiesForSelection(Request $request)
    {
        try {
            $query = City::query();
            if ($request->has('state_id') && $request->state_id != '') {
                $query->where('state_id', $request->state_id);
            }
            $cities = $query->select('id', 'name_ar', 'name_en')
                ->where('activation', true)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'Cities Retrieved',
                'data' => $cities
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCitiesForSelection of dashboard cities ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return  Excel::download(new CitiesExport($request), 'cities.xlsx');
        } catch (\Exception $e) {
            Log::error('error in colors in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
