<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Shared\Cities\GetCitiesRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ValidationError;
use App\Models\City;
use App\Models\Country;
use App\Repositories\CitiesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlacesController extends BaseController
{
    private $lang;
    /**
     * @var CitiesRepository
     */
    private $citiesRepository;

    public function __construct(Request $request, CitiesRepository $citiesRepository)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->citiesRepository = $citiesRepository;
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

    public function getStates(): \Illuminate\Http\JsonResponse
    {
        try {
            $states = DB::table('states')
                ->join('regions', 'regions.id', '=', 'states.region_id')
                ->join('countries', 'countries.id', '=', 'regions.country_id')
                ->select('states.id', 'states.name_' . $this->lang . ' as name')
                //   ->where('countries.id', $country_id)
                ->where('states.activation', true)
                ->orderBy('states.name_' . $this->lang)
                ->get();
            return response()->json([
                "status" => true,
                'message' => trans('messages.sections.states'),
                "data" => $states
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStates of seller places' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function getCities(GetCitiesRequest $request): \Illuminate\Http\JsonResponse
    {
        try {

            return response()->json([
                "status" => true,
                'message' => trans('messages.sections.cities'),
                "data" => $this->citiesRepository->getCitiesForSelection($this->lang, $request['state_id'])
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCities of seller places' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }
}
