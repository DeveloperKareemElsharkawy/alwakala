<?php

namespace App\Http\Controllers\Dashboard\Places;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\CountriesExport;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Places\Countries\CreateCountriesRequest;
use App\Http\Requests\Places\Countries\UpdateCountriesRequest;
use App\Http\Requests\Places\DeleteRequest;
use App\Lib\Log\ServerError;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CountriesController extends BaseController
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
            $query = Country::query()
                ->orderByRaw('countries.updated_at DESC NULLS LAST');
            /*  if ($request->filled("name_ar")) {
                  $searchQuery = "%" . $request->get("name_ar") . "%";
                  $query->where('name_ar', "ilike", $searchQuery);
              }
              if ($request->filled("name_en")) {
                  $searchQuery = "%" . $request->get("name_en") . "%";
                  $query->where('name_en', "ilike", $searchQuery);
              }*/
            if ($request->filled("name")) {
                $searchQuery = "%" . $request->get("name") . "%";
                $query->where('name_ar', "ilike", $searchQuery)
                    ->orWhere('name_en', "ilike", $searchQuery);
            }
            if ($request->filled('activation')) {
                $query->where('activation', $request->activation);
            }
            if ($request->filled('id')) {
                $query->where('id', intval($request->id));
            }
            if ($request->filled('iso')) {
                $query->where('iso', $request->iso);
            }
            if ($request->filled('country_code')) {
                $query->where('country_code', $request->country_code);
            }
            if ($request->filled("sort_by_name_ar")) {
                $query->where('name_ar', $request->sort_by_name_ar);
            }
            if ($request->filled("sort_by_name_en")) {
                $query->where('name_en', $request->sort_by_name_en);
            }
            if ($request->filled('sort_by_activation')) {
                $query->where('activation', $request->sort_by_activation);
            }
            if ($request->filled('sort_by_id')) {
                $query->where('id', $request->sort_by_id);
            }
            if ($request->filled('sort_by_iso')) {
                $query->where('iso', $request->sort_by_iso);
            }
            if ($request->filled('sort_by_country_code')) {
                $query->where('country_code', $request->sort_by_country_code);
            }
            $count = $query->get()->count();
            $countries = $query->offset($offset)->limit($limit)->get();
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $countries,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,

            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard countries ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateCountriesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCountriesRequest $request)
    {
        try {
            $country = new Country;
            $country->name_ar = $request->name_ar;
            $country->name_en = $request->name_en;
            $country->activation = $request->activation;
            $country->save();
            $logData['id'] = $country->id;
            $logData['ref_name_ar'] = $country->name_ar;
            $logData['ref_name_en'] = $country->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_COUNTRY;
            event(new DashboardLogs($logData, 'countries'));
            return response()->json([
                'success' => true,
                'message' => 'Country created',
                'data' => $country
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard countries ' . __LINE__ . $e);
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
            $category = Country::query()->find($id);

            return response()->json([
                'success' => true,
                'message' => 'Country',
                'data' => $category
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard countries ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateCountriesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCountriesRequest $request)
    {
        try {
            $country = Country::query()
                ->find($request->id);
            $country->name_ar = $request->name_ar;
            $country->name_en = $request->name_en;
            $country->iso = $request->iso;
            $country->country_code = $request->country_code;
            $country->phone_code = $request->phone_code;
            $country->activation = $request->activation;

            $country->save();
            $logData['id'] = $country->id;
            $logData['ref_name_ar'] = $country->name_ar;
            $logData['ref_name_en'] = $country->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_COUNTRY;
            event(new DashboardLogs($logData, 'countries'));
            return response()->json([
                'success' => true,
                'message' => 'Country Updated',
                'data' => $country
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard countries ' . __LINE__ . $e);
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
            $containRegions = Region::query()
                ->where('country_id', $request['id'])
                ->first();
            if ($containRegions) {
                return response()->json([
                    'success' => false,
                    'message' => 'this country contain regions delete them first',
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $country=Country::query()->where('id', $request->get('id'))->first();
            Country::destroy($request->get('id'));
            $logData['id'] = $request->get('id');
            $logData['ref_name_ar'] = $country->name_ar;
            $logData['ref_name_en'] = $country->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_COUNTRY;
            event(new DashboardLogs($logData, 'countries'));
            return response()->json([
                'success' => true,
                'message' => 'Country deleted',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard countries ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountriesForSelection()
    {
        try {
            $countries = Country::query()
                ->select('id', 'name_ar', 'name_en')
                ->where('activation', true)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'Countries Retrieved',
                'data' => $countries
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCountriesForSelection of dashboard countries ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return  Excel::download(new CountriesExport($request), 'countries.xlsx');
        } catch (\Exception $e) {
            Log::error('error in colors in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
