<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellerApp\AddSettingRequest;
use App\Lib\Log\ServerError;
use App\Models\Store;
use App\Repositories\StoreSettingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreSettingController extends BaseController
{

    public function store(AddSettingRequest $request)
    {
        try {
            $data = $request->all();
            $store = Store::query()
                ->select('id')
                ->where('user_id', $request['user_id'])->first();
            $data['store_id'] = $store->id;
            $storeSetting = StoreSettingRepository::store($data);
            if (!$storeSetting) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            return response()->json([
                'status' => true,
                'message' => trans('messages.system.created'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in store of seller StoreSetting' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function list(Request $request)
    {
        try {
            $store = Store::query()
                ->select('id')
                ->where('user_id', $request['user_id'])->first();
            $storeSetting = StoreSettingRepository::list($store->id);
            if (!$storeSetting) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            return response()->json([
                'status' => true,
                'message' => trans('messages.system.retrieved'),
                'data' => $storeSetting
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in list of seller StoreSetting' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
