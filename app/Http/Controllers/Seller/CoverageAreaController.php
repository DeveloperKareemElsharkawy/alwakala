<?php

namespace App\Http\Controllers\Seller;

use App\Enums\APlaces;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\SellerApp\CreateStoreRequest;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CityStore;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CoverageAreaController extends BaseController
{
    public function getStoreCoverArea(Request $request)
    {
        try {
            // TODO implement it for other users send request with store_id
            $storeId = Store::query()
                ->where('user_id', $request->user_id)
                ->first()->id;

            $coverAreas = CityStore::query()
                ->select('city_store.id as id', 'city_store.fees', 'city_store.city_id',
                    'cities.name_ar as city_name_ar', 'cities.name_en as city_name_en',
                    'states.id as state_id', 'states.name_ar as state_name_ar', 'states.name_en as state_name_en',
                    'regions.id as region_id', 'regions.name_ar as region_name_ar', 'regions.name_en as region_name_en',
                    'countries.id as country_id', 'countries.name_ar as country_name_ar', 'countries.name_en as country_name_en'
                )
                ->where('store_id', $storeId)
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
                "message" => trans('messages.stores.store_cover_area'),
                "data" => $response,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStoreCoverArea of seller StoreCoverArea' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function addCoverArea(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cover_area' => 'required|numeric',
                'fees' => 'required|numeric|min:0'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $storeId = Store::query()->where('user_id', $request->user_id)->first()->id;
            $data = $request->all();
            $data['store_id'] = $storeId;

            if ($request->cover_area == APlaces::COUNTRY) {
                $validator = Validator::make($request->all(), [
                    'place_id' => 'required|numeric|exists:countries,id'
                ]);
                if ($validator->fails()) {
                    return ValidationError::handle($validator);
                }
                $this->getCountryCities($data);
            } elseif ($request->cover_area == APlaces::STATE) {
                $validator = Validator::make($request->all(), [
                    'place_id' => 'required|numeric|exists:states,id',
                ]);
                if ($validator->fails()) {
                    return ValidationError::handle($validator);
                }
                $this->getStateCities($data);
            } elseif ($request->cover_area == APlaces::CITY) {
                $validator = Validator::make($request->all(), [
                    'place_id' => 'required|numeric|exists:cities,id',
                ]);
                if ($validator->fails()) {
                    return ValidationError::handle($validator);
                }
                $this->getCities($data);
            }
            return response()->json([
                "status" => true,
                "message" => trans('messages.actions.store_area_added'),
                "data" => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in addCoverArea of seller StoreCoverArea' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateCoverArea(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:city_store,id',
                'city_id' => 'required|numeric|exists:cities,id',
                'fees' => 'required|numeric|min:0'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $cityStore = CityStore::query()
                ->where('id', $request->id)
                ->update(['fees' => $request->fees, 'city_id' => $request->city_id]);


            if (!$cityStore) {
                return response()->json([
                    "status" => false,
                    "message" => "Error",
                    "data" => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            return response()->json([
                "status" => true,
                "message" => trans('messages.actions.store_area_updated'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in updateCoverArea of seller StoreCoverArea' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function deleteCoverArea(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:city_store,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $cityStore = CityStore::destroy($request->id);


            if (!$cityStore) {
                return response()->json([
                    "status" => false,
                    "message" => trans('messages.actions.store_area_not_found'),
                    "data" => ''
                ], AResponseStatusCode::SUCCESS);
            }
            return response()->json([
                "status" => true,
                "message" => trans('messages.actions.store_area_deleted'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in deleteCoverArea of seller StoreCoverArea' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    private function getCountryCities($data)
    {
        $query = DB::table('cities')
            ->join('states', 'states.id', '=', 'cities.state_id')
            ->join('regions', 'regions.id', '=', 'states.region_id')
            ->join('countries', 'countries.id', '=', 'regions.country_id')
            ->select('cities.id')
            ->where('countries.id', $data['place_id'])
            ->get();

        $newArray = [];
        foreach ($query as $index => $value) {
            $newArray[$index]['city_id'] = $value->id;
            $newArray[$index]['store_id'] = $data['store_id'];
            $newArray[$index]['fees'] = $data['fees'];
            $newArray[$index]['created_at'] = Carbon::now();
            $newArray[$index]['updated_at'] = Carbon::now();
        }

        DB::beginTransaction();
        if (!CityStore::query()->insert($newArray)) {
            DB::rollBack();
        }
        DB::commit();
    }

    private function getStateCities($data)
    {
        $query = DB::table('cities')
            ->join('states', 'states.id', '=', 'cities.state_id')
            ->select('cities.id')
            ->where('states.id', $data['place_id'])
            ->get();

        $newArray = [];
        foreach ($query as $index => $value) {
            $newArray[$index]['city_id'] = $value->id;
            $newArray[$index]['store_id'] = $data['store_id'];
            $newArray[$index]['fees'] = $data['fees'];
            $newArray[$index]['created_at'] = Carbon::now();
            $newArray[$index]['updated_at'] = Carbon::now();
        }
        DB::beginTransaction();
        if (!CityStore::query()->insert($newArray)) {
            DB::rollBack();
        }
        DB::commit();
    }

    private function getCities($data)
    {
        $newArray = [];
        $newArray['city_id'] = $data['place_id'];
        $newArray['store_id'] = $data['store_id'];
        $newArray['fees'] = $data['fees'];
        CityStore::query()->insert($newArray);
    }
}
