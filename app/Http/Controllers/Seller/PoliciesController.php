<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PoliciesController extends BaseController
{
    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function getPolicies(Request $request)
    {
        try {
            $policies = Policy::query()
                ->select('id', 'name_' . $this->lang.' as name', 'description_' . $this->lang.' as description')
                ->get();
            return response()->json([
                'status' => true,
                'message' => trans('messages.policy.get_policies'),
                'data' => $policies
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in getPolicies of seller Policies' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
