<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Consumer\Address\CreateAddressRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ValidationError;

;

use App\Repositories\UserAddressesRepository;
use App\Services\UserAddressesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends BaseController
{
    /**
     * @var string
     */
    private $lang;
    /**
     * @var UserAddressesRepository
     */
    private $userAddressesRepository;
    /**
     * @var null
     */
    private $userId;
    /**
     * @var UserAddressesService
     */
    private $userAddressesService;

    /**
     * UserAddressController constructor.
     * @param UserAddressesRepository $userAddressesRepository
     * @param UserAddressesService $userAddressesService
     * @param Request $request
     */
    public function __construct(UserAddressesRepository $userAddressesRepository,UserAddressesService $userAddressesService, Request $request)
    {
        $this->userAddressesRepository = $userAddressesRepository;
        $this->userAddressesService = $userAddressesService;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->userId = UserId::UserId($request);
    }

    /**
     * Display a listing of the addresses.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = UserId::UserId($request);
            $data = $this->userAddressesRepository->getUserAddresses($userId, $this->lang);

            return response()->json([
                "status" => true,
                'message' => trans('messages.addresses.retrieved_all'),
                "data" => $data
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in get user addresses ' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }


    /**
     * Store a newly created Address in storage.
     * @param CreateAddressRequest $request
     * @return JsonResponse
     */
    public function store(CreateAddressRequest $request): JsonResponse

    {
        try {
            $request['user_id'] = $this->userId;
            $this->userAddressesRepository->createUserAddress($request);
            return response()->json([
                "status" => true,
                'message' => trans('messages.addresses.added'),
                "data" => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in save user address ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Display the specified Address.
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $validation = $this->validateAddressId($request);
            if (!is_bool($validation)) {
                return $validation;
            }
            if (!$this->userAddressesRepository->checkIfAddressBelongsToUser($request['address_id'], $this->userId)) {
                return response()->json(
                    [
                        "message" => "access denied"
                    ]
                    , 403);
            }
            $data = $this->userAddressesRepository->getUserAddress($request['address_id'], $this->lang);

            return response()->json([
                "status" => true,
                'message' => trans('messages.addresses.retrieved'),
                "data" => $data
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in get user address ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * Update the specified Address .
     * @param CreateAddressRequest $request
     * @return JsonResponse
     */
    public function update(CreateAddressRequest $request): JsonResponse
    {
        try {
            $validation = $this->validateAddressId($request);
            if (!is_bool($validation)) {
                return $validation;
            }
            if (!$this->userAddressesRepository->checkIfAddressBelongsToUser($request['address_id'], $this->userId)) {
                return response()->json(
                    [
                        "message" => "access denied"
                    ]
                    , 403);
            }
            $this->userAddressesRepository->editUserAddress($request);
            return response()->json([
                "status" => true,
                'message' => trans('messages.addresses.edited'),
                "data" => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {

            Log::error('error in update address ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Remove the specified address.
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $validation = $this->validateAddressId($request);
            if (!is_bool($validation)) {
                return $validation;
            }
            if (!$this->userAddressesRepository->checkIfAddressBelongsToUser($request['address_id'], $this->userId)) {
                return response()->json(
                    [
                        "message" => "access denied"
                    ]
                    , 403);
            }
            $this->userAddressesRepository->deleteUserAddress($request['address_id']);
            return response()->json([
                "status" => true,
                'message' => trans('messages.addresses.deleted'),
                "data" => []
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in delete address ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Set address as default address.
     * @param Request $request
     * @return JsonResponse
     */
    public function setDefaultAddress(Request $request): JsonResponse
    {
        try {
            $validation = $this->validateAddressId($request);
            if (!is_bool($validation)) {
                return $validation;
            }
            if (!$this->userAddressesRepository->checkIfAddressBelongsToUser($request['address_id'], $this->userId)) {
                return response()->json(
                    [
                        "message" => "access denied"
                    ]
                    , 403);
            }
            $request['user_id'] = $this->userId;
            $this->userAddressesService->setDefaultAddress($request);
            return response()->json([
                "status" => true,
                'message' => trans('messages.addresses.set_default'),
                "data" => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in set default address ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * Validate address id.
     * @param $request
     * @return false|JsonResponse
     */
    private function validateAddressId($request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|numeric|exists:addresses,id',
        ]);
        if ($validator->fails()) {
            return ValidationError::handle($validator);
        }
        return false;
    }
}
