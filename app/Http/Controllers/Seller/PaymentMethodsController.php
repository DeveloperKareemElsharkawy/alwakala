<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Seller\Payment\PaymentMethodResource;
use App\Lib\Log\ServerError;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class PaymentMethodsController extends BaseController
{
    public function getPaymentMethods(Request $request)
    {
        try {
            $PaymentMethods = PaymentMethod::query()->get();
            return response()->json([
                'status' => true,
                'message' => trans('messages.inventory.payment_methods'),
                'data' => PaymentMethodResource::collection($PaymentMethods)
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in getPaymentMethods of seller PaymentMethods' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
