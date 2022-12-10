<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Models\ShipmentMethod;
use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class ShippingMethodsController extends BaseController
{
    private $lang;

    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }


    public function getShipmentMethods(Request $request)
    {
        try {
            $ShipmentMethods = ShippingMethod::query()->select('id', 'name_' . $this->lang.' as name', 'description_' . $this->lang.' as description')->get();
            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.shipping_method'),
                'data' => $ShipmentMethods
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in getShipmentMethods of seller ShippingMethods' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
