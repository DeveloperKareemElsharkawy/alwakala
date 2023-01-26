<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellerApp\Coupons\CouponRequest;
use App\Services\Coupons\CouponsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends BaseController
{
    public $couponsService;

    public function __construct(CouponsService $couponsService)
    {
        $this->couponsService = $couponsService;
    }

    public function addCoupon(CouponRequest $request)
    {
        try {
            $data = $request->all();
            $data['seller_id'] = $request->user_id;
            $data['status'] = true;

            $coupon = $this->couponsService->create($data);

            return response()->json([
                'status' => true,
                'message' => "coupon Create",
                'data' => $coupon,
            ]);
        } catch (\Exception $e) {
            Log::error('error in add coupon of seller' . __LINE__ . $e);

            return $this->connectionError($e);
        }
    }

    public function editCoupon(CouponRequest $request)
    {
        try {

            $data = $request->all();
            $coupon = $this->couponsService->edit($data);

            return response()->json([
                'status' => true,
                'message' => "coupon Create",
                'data' => $coupon,
            ]);
        } catch (\Exception $e) {
            Log::error('error in add coupon of seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCoupons(Request $request)
    {
        try {
            $data = $this->couponsService->list($request);

            return response()->json([
                'status' => true,
                'message' => 'Coupons',
                'data' => $data,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCoupons of Seller' . __LINE__ . $e);

            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCoupon(Request $request, $id)
    {
        // dd($id);
        try {
            $data = $this->couponsService->show($request, $id);
            // dd($data);
            return response()->json([
                'status' => true,
                'message' => 'Coupon',
                'data' => $data,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCoupons of Seller ' . __LINE__ . $e);

            return $this->connectionError($e);
        }
    }

    public function activate(Request $request, $id)
    {
        try {
            $coupon = $this->couponsService->activate($request, $id);

            return response()->json([
                'status' => true,
                'message' => "coupon activation changed",
                'data' => $coupon,
            ]);
        } catch (\Exception $e) {
            Log::error('error in coupon activation changed of seller' . __LINE__ . $e);

            return $this->connectionError($e);
        }
    }

    public function activatedCoupons(Request $request)
    {
        try {
            $coupon = $this->couponsService->activatedCoupons($request);

            return response()->json([
                'status' => true,
                'message' => "Activated coupons",
                'data' => $coupon,
            ]);
        } catch (\Exception $e) {
            Log::error('error in getActiveCoupons of seller' . __LINE__ . $e);
            return $e->getMessage();
            return $this->connectionError($e);
        }
    }

    public function inactiveCoupons(Request $request)
    {
        try {
            $coupon = $this->couponsService->inactiveCoupons($request);

            return response()->json([
                'status' => true,
                'message' => "Inactive coupons",
                'data' => $coupon,
            ]);
        } catch (\Exception $e) {
            Log::error('error in getInActiveCoupons of seller' . __LINE__ . $e);

            return $this->connectionError($e);
        }
    }
}
