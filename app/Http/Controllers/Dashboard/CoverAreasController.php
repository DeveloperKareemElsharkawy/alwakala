<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Dashboard\CoverArea\ListStoreCoverAreaRequest;
use App\Lib\Log\ServerError;
use App\Models\CityStore;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CoverAreasController extends BaseController
{

    public function getStoreCoverArea($store_id)
    {
        try {
            $coverAreas = CityStore::query()
                ->select('city_store.id as id', 'city_store.fees', 'city_store.city_id',
                    'cities.name_ar as city_name_ar', 'cities.name_en as city_name_en',
                    'states.id as state_id', 'states.name_ar as state_name_ar', 'states.name_en as state_name_en',
                    'regions.id as region_id', 'regions.name_ar as region_name_ar', 'regions.name_en as region_name_en',
                    'countries.id as country_id', 'countries.name_ar as country_name_ar', 'countries.name_en as country_name_en'
                )
                ->where('store_id', $store_id)
                ->join('cities', 'cities.id', '=', 'city_store.city_id')
                ->join('states', 'states.id', '=', 'cities.state_id')
                ->join('regions', 'regions.id', '=', 'states.region_id')
                ->join('countries', 'countries.id', '=', 'regions.country_id')
                ->get();

            $response = [
                'countries' => []
            ];

            $countryKeys = [];
//            $regionKeys = [];
            $stateKeys = [];
            $cityKeys = [];
            $index = 0;
            foreach ($coverAreas as $coverArea) {
                if (!array_key_exists($coverArea->country_id, $countryKeys)) {
                    $countryKeys[$coverArea->country_id] = $index++;
                    $countryObject = new \stdClass();
                    $countryObject->country_id = $coverArea->country_id;
                    $countryObject->country_name_ar = $coverArea->country_name_ar;
                    $countryObject->country_name_en = $coverArea->country_name_en;
                    $countryObject->states = [];
                    array_push($response['countries'], $countryObject);
                }
                // TODO for future implementations
//                if (!array_key_exists($coverArea->region_id, $regionKeys)) {
//                    $regionKeys[$coverArea->region_id] = $index++;
//                    $regionObject = new \stdClass();
//                    $regionObject->region_id = $coverArea->region_id;
//                    $regionObject->region_name_ar = $coverArea->region_name_ar;
//                    $regionObject->region_name_en = $coverArea->region_name_en;
//                    $regionObject->states = [];
//                    array_push($countryObject->regions, $regionObject);
//                }
                if (!array_key_exists($coverArea->state_id, $stateKeys)) {
                    $stateKeys[$coverArea->state_id] = $index++;

                    $stateObject = new \stdClass();
                    $stateObject->state_id = $coverArea->state_id;
                    $stateObject->state_name_ar = $coverArea->state_name_ar;
                    $stateObject->state_name_en = $coverArea->state_name_en;
                    $stateObject->cities = [];
                    array_push($countryObject->states, $stateObject);
                }
                if (!array_key_exists($coverArea->city_id, $cityKeys)) {
                    $cityKeys[$coverArea->city_id] = $index++;

                    $cityObject = new \stdClass();
                    $cityObject->id = $coverArea->id;
                    $cityObject->city_id = $coverArea->city_id;
                    $cityObject->city_name_ar = $coverArea->city_name_ar;
                    $cityObject->city_name_en = $coverArea->city_name_en;
                    $cityObject->fees = $coverArea->fees;
                    array_push($stateObject->cities, $cityObject);
                }
            }
            return response()->json([
                "status" => true,
                "message" => "store cover area",
                "data" => $response,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStoreCoverArea of dashboard CoverAreas' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
