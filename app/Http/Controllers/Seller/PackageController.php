<?php

namespace App\Http\Controllers\Seller;

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

    public function subscribeToPackage(SubscribeToPackageRequest $request)
    {
        try {
            if (!$this->packagesRepository->subscribeToPackage($request)) {
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.packages.forbidden'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }
            return response()->json([
                'status' => true,
                'message' => trans('messages.packages.add'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in subscribeToPackage of seller Package' . __LINE__ . $e);
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
            Log::error('error in getPackagesByStoreTypeId of seller Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getPackageSubscribeByStore(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Package',
                'data' => $this->packagesRepository->getPackageSubscribeByStore($request)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getPackagesByStoreTypeId of dashboard Package' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
