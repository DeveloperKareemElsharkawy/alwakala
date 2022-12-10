<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Commission\ChangeCommissionStatusRequest;
use App\Http\Requests\Commission\CreateCommissionRequest;
use App\Http\Requests\Commission\PaidCommissionRequest;
use App\Lib\Log\ValidationError;
use App\Repositories\CommissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommissionController extends BaseController
{
    /**
     * @var CommissionRepository
     */
    private $commissionRepository;

    public function __construct(CommissionRepository $commissionRepository)
    {
        $this->commissionRepository = $commissionRepository;
    }

    public function createCommission(CreateCommissionRequest $request)
    {
        try {
            $this->commissionRepository->createCommission($request);
            return response()->json([
                'status' => true,
                'message' => 'Commission Created Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in createCommission of dashboard commission' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateCommission(CreateCommissionRequest $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:commissions,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $this->commissionRepository->updateCommission($request);
            return response()->json([
                'status' => true,
                'message' => 'Commission Updated Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateCommission of dashboard commission' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function deleteCommission($id)
    {
        try {
            $this->commissionRepository->deleteCommission($id);
            return response()->json([
                'status' => true,
                'message' => 'Commission Deleted Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in deleteCommission of dashboard commission' . __LINE__ . $e);
            return $this->connectionError($e);
        }


    }

    public function changeCommissionStatus(ChangeCommissionStatusRequest $request)
    {
        try {
            if (!$this->commissionRepository->changeCommissionStatus($request)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Status Cannot Changed',
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }
            return response()->json([
                'status' => true,
                'message' => 'Status Changed Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in changeCommissionStatus of dashboard commission' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function paidCommission(PaidCommissionRequest $request)
    {
        try {

            $this->commissionRepository->paidCommission($request);
            return response()->json([
                'status' => true,
                'message' => 'Payment Done Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in paidCommission of dashboard commission' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function listAllStoresCommissions()
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'All Stores',
                'data' => $this->commissionRepository->listAllStoresCommissions()
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in listAllCommissions of dashboard commission' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function listAllCommissions()
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'All Commissions',
                'data' => $this->commissionRepository->listAllCommissions()
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in listAllCommissions of dashboard commission' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
