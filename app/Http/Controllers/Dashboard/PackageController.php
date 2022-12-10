<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Packages\AddPackageRequest;
use App\Http\Requests\Packages\changeActivePackageStatusRequest;
use App\Http\Requests\Packages\SubscribeToPackageRequest;
use App\Lib\Log\ValidationError;
use App\Repositories\PackagesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PackageController extends BaseController
{
    /**
     * @var PackagesRepository
     */
    private $packagesRepository;

    public function __construct(PackagesRepository $packagesRepository)
    {
        $this->packagesRepository = $packagesRepository;
    }

    public function addPackage(AddPackageRequest $request)
    {
        try {
            $this->packagesRepository->addPackage($request);
            return response()->json([
                'status' => true,
                'message' => 'Package Added Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in addPackage of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updatePackage(AddPackageRequest $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:packages,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $this->packagesRepository->addPackage($request);
            return response()->json([
                'status' => true,
                'message' => 'Package Updated Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updatePackage of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function deletePackage($id)
    {
        try {
            if (!$this->packagesRepository->deletePackage($id)) {
                return response()->json([
                    'status' => true,
                    'message' => 'You Cannot Delete This Packages',
                    'data' => []
                ], AResponseStatusCode::FORBIDDEN);
            }
            return response()->json([
                'status' => true,
                'message' => 'Package Deleted Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in deletePackage of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function subscribeToPackage(SubscribeToPackageRequest $request)
    {
        try {
            if (!$this->packagesRepository->subscribeToPackage($request, true)) {
                return response()->json([
                    'status' => true,
                    'message' => 'You Cannot Subscribe in two Packages',
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }

            return response()->json([
                'status' => true,
                'message' => 'Subscribe To Package Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in subscribeToPackage of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function changePackageStatus(changeActivePackageStatusRequest $request)
    {
        try {
            $this->packagesRepository->changePackageStatus($request);
            return response()->json([
                'status' => true,
                'message' => 'Package Status Changed Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in changePackageStatus of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function changePackageStatusToStore(SubscribeToPackageRequest $request)
    {
        try {
            if (!$this->packagesRepository->changePackageStatusToStore($request)) {
                return response()->json([
                    'status' => true,
                    'message' => 'You Cannot Active This Subscribe To Package',
                    'data' => []
                ], AResponseStatusCode::FORBIDDEN);
            }
            return response()->json([
                'status' => true,
                'message' => 'Subscribe To Package Active Successfully',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in subscribeToPackage of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getPackagesByStoreTypeId($type)
    {
        try {

            return response()->json([
                'status' => true,
                'message' => 'All Packages',
                'data' => $this->packagesRepository->getPackagesByStoreTypeId($type)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getPackagesByStoreTypeId of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSubscribersStores($type)
    {
        try {

            return response()->json([
                'status' => true,
                'message' => 'All Stores',
                'data' => $this->packagesRepository->getSubscribersStores($type)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getPackagesByStoreTypeId of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getPackageSubscribeByStore(SubscribeToPackageRequest $request)
    {
        try {

            return response()->json([
                'status' => true,
                'message' => 'Package',
                'data' => $this->packagesRepository->getPackageSubscribeByStore($request, true)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getPackagesByStoreTypeId of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
