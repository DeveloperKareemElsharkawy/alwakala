<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\SellerApp\AddAddressRequest;
use App\Http\Requests\SellerApp\DeleteAddressRequest;
use App\Http\Requests\SellerApp\EditAddressRequest;
use App\Lib\Helpers\Authorization\AuthorizationHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Address;
use App\Models\Store;
use App\Repositories\ProductRepository;
use App\Repositories\ProfileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AddressesController extends BaseController
{

    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function addAddress(AddAddressRequest $request)
    {
        try {
            $sellerAddress = new Address;
            $sellerAddress->name = $request->name;
            $sellerAddress->type = $request->type;
            $sellerAddress->user_id = $request->user_id;
            $sellerAddress->mobile = $request->mobile;
            $sellerAddress->address = $request->address;
            $sellerAddress->city_id = $request->city_id;
            $sellerAddress->latitude = $request->latitude ?? 1.1;
            $sellerAddress->longitude = $request->longitude ?? 1.1;
            $sellerAddress->building_no = $request->building_no ?? 0;
            $sellerAddress->landmark = $request->landmark ?? '';
            $sellerAddress->main_street = $request->main_street ?? '';
            $sellerAddress->side_street = $request->side_street ?? '';
            $sellerAddress->is_default = $request->is_default ?? false;
            $sellerAddress->save();

            return response()->json([
                'status' => true,
                'message' => trans('messages.addresses.added'),
                'data' => $sellerAddress,
            ]);
        } catch (\Exception $e) {
            Log::error('error in addAddress of seller Address' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getAddresses(Request $request)
    {
        try {
            $storeAddress = Store::query()
                ->select(
                    'stores.*',
                    'cities.name_' . $this->lang . ' as city_name',
                    'states.id as state_id', 'states.name_' . $this->lang . ' as state_name',
                    'countries.id as country_id', 'countries.name_' . $this->lang . ' as country_name'
                )
                ->where('stores.user_id', $request->user_id)
                ->join('cities', 'cities.id', '=', 'stores.city_id')
                ->join('states', 'states.id', '=', 'cities.state_id')
                ->join('regions', 'regions.id', '=', 'states.region_id')
                ->join('countries', 'countries.id', '=', 'regions.country_id')
                ->first();
            if ($request->header('X-localization') == 'ar') {
                $storeAddress->name = 'عنوان المتجر';
            } else {
                $storeAddress->name = 'Store Address';
            }

            $otherAddresses = Address::query()
                ->select(
                    'addresses.id as address_id',
                    'addresses.user_id',
                    'addresses.name',
                    'addresses.type',
                    'addresses.address',
                    'addresses.latitude',
                    'addresses.longitude',
                    'addresses.building_no',
                    'addresses.landmark',
                    'addresses.main_street',
                    'addresses.side_street',
                    'addresses.city_id',
                    'addresses.mobile',
                    'cities.name_' . $this->lang . ' as city_name',
                    'states.id as state_id', 'states.name_' . $this->lang . ' as state_name',
                    'countries.id as country_id', 'countries.name_' . $this->lang . ' as country_name')
                ->where('addresses.user_id', $request->user_id)
                ->join('cities', 'cities.id', '=', 'addresses.city_id')
                ->join('states', 'states.id', '=', 'cities.state_id')
                ->join('regions', 'regions.id', '=', 'states.region_id')
                ->join('countries', 'countries.id', '=', 'regions.country_id')
                ->orderByDesc('addresses.is_default')
                ->get();

            return response([
                'status' => true,
                'message' => trans('messages.addresses.retrieved_all'),
                'data' => [
                    'store_address' => $storeAddress,
                    'other_addresses' => $otherAddresses
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('error in getAddresses of seller Address' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getAddress(Request $request, $id)
    {
        try {
            if (!AuthorizationHelper::isAuthorized('id', $id, 'user_id', $request->user_id, Address::class)) {
                return response()->json([
                    'status' => false,
                    'message' => 'wrong address',
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            $address = Address::query()
                ->where('id', $id)
                ->first();

            return response([
                'status' => true,
                'message' => trans('messages.addresses.retrieved'),
                'data' => $address,
            ]);
        } catch (\Exception $e) {
            Log::error('error in getAddress of seller Address' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function editAddress(EditAddressRequest $request)
    {
        try {
            if (!AuthorizationHelper::isAuthorized('id', $request->id, 'user_id', $request->user_id, Address::class)) {
                return response()->json([
                    'status' => false,
                    'message' => 'wrong address',
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            $sellerAddress = Address::query()
                ->where('id', $request->id)
                ->first();

            $sellerAddress->name = $request->name;
            $sellerAddress->mobile = $request->mobile;
            $sellerAddress->address = $request->address;
            $sellerAddress->city_id = $request->city_id;
            $sellerAddress->latitude = $request->latitude;
            $sellerAddress->longitude = $request->longitude;
            $sellerAddress->building_no = $request->building_no;
            $sellerAddress->landmark = $request->landmark;
            $sellerAddress->main_street = $request->main_street;
            $sellerAddress->side_street = $request->side_street;
            $sellerAddress->save();

            return response()->json([
                'status' => true,
                'message' => trans('messages.addresses.edited'),
                'data' => '',
            ]);

        } catch (\Exception $exception) {
            return ServerError::handle($exception);
        }
    }

    public function deleteAddress(DeleteAddressRequest $request)
    {
        try {
            if (!AuthorizationHelper::isAuthorized('id', $request->id, 'user_id', $request->user_id, Address::class)) {
                return response()->json([
                    'status' => false,
                    'message' => 'wrong address',
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            Address::query()->find($request->id)->delete();
            return response()->json([
                'status' => true,
                'message' => trans('messages.addresses.deleted'),
                'data' => '',
            ]);

        } catch (\Exception $exception) {
            return ServerError::handle($exception);
        }
    }

    public function changeDefaultAddress(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            Address::query()
                ->where('user_id', $request->user_id)
                ->update(['is_default' => false]);

            if ($request->id != 0) {
                if (!AuthorizationHelper::isAuthorized('id', $request->id, 'user_id', $request->user_id, Address::class)) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('messages.addresses.wrong_address'),
                        'data' => ''
                    ], AResponseStatusCode::FORBIDDEN);
                }
                $sellerAddress = Address::query()
                    ->where('id', $request->id)
                    ->first();
                $sellerAddress->is_default = true;
                $sellerAddress->save();
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.addresses.set_default'),
                'data' => '',
            ]);

        } catch (\Exception $exception) {
            return ServerError::handle($exception);
        }
    }
}
