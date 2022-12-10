<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\Controller;
use App\Services\Coupons\couponsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{

    public $couponsService;
    public function __construct(CouponsService $couponsService)
    {
        $this->couponsService = $couponsService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCoupons(Request $request)
    {
        try {
            $data = $this->couponsService->listAll($request);

            return response()->json([
                'status' => true,
                'message' => 'Coupons',
                'data' => $data,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCoupons of Dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
