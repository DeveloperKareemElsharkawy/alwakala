<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlacesController extends BaseController
{
    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function getCountries(): \Illuminate\Http\JsonResponse
    {
        try {
            $countries = Country::query()
                ->select('id', 'name_' . $this->lang . ' as name')
                ->where('activation', true)
                ->get();

            return response()->json([
                'status' => true,
                'message' => trans('messages.sections.countries'),
                'data' => $countries
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCountries of seller places' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStates($country_id): \Illuminate\Http\JsonResponse
    {
        try {
            $states = DB::table('states')
                ->join('regions', 'regions.id', '=', 'states.region_id')
                ->join('countries', 'countries.id', '=', 'regions.country_id')
                ->select('states.id', 'states.name_' . $this->lang . ' as name')
                ->where('countries.id', $country_id)
                ->where('states.activation', true)
                ->get();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                'message' => trans('messages.sections.states'),
                "data" => $states
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStates of seller places' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function getCities($state_id): \Illuminate\Http\JsonResponse
    {
        try {
            $cities = City::query()
                ->select('id', 'name_' . $this->lang . ' as name')
                ->where('state_id', $state_id)
                ->where('activation', true)
                ->get();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                'message' => trans('messages.sections.cities'),
                "data" => $cities
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCities of seller places' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }
}
