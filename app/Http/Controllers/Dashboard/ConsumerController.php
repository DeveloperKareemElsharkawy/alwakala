<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\AStatusCodeResponse;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Consumer\EmailRequest;
use App\Http\Requests\Consumer\UpdateConsumerRequest;
use App\Http\Requests\Shared\LoginRequest;
use App\Http\Resources\Dashboard\ConsumerResource;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Consumer;
use App\Models\User;
use App\Repositories\Dashboard\ConsumerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConsumerController extends BaseController
{
    /**
     * @var ConsumerRepository
     */
    private $consumerRepository;

    /**
     * ConsumerController constructor.
     * @param ConsumerRepository $consumerRepository
     */
    public function __construct(ConsumerRepository $consumerRepository)
    {
        $this->consumerRepository = $consumerRepository;
    }

    /**
     * List all consumers.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return response()->json([
                "status" => true,
                "message" => "consumers retrieved successfully",
                "consumers" => $this->consumerRepository->index(),
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard consumer' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Show consumer by id.
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $validation = $this->validateData($request);
        if (!is_bool($validation)) {
            return $validation;
        }
        try {
            return response()->json([
                "status" => true,
                "message" => "consumer retrieved successfully",
                "consumers" => $this->consumerRepository->view($request->id)
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in view of dashboard consumer' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Update consumer activation status.
     * @param Request $request
     * @return JsonResponse
     */
    public function changeConsumerStatus(Request $request): JsonResponse
    {
        try {
            $validation = $this->validateData($request);
            if (!is_bool($validation)) {
                return $validation;
            }
            return response()->json([
                "status" => true,
                "message" => "status updated successfully",
                "consumers" => $this->consumerRepository->changeConsumerStatus($request)
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in change consumer status of dashboard consumer' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Update consumer.
     * @param UpdateConsumerRequest $request
     * @return JsonResponse
     */
    public function update(UpdateConsumerRequest $request): JsonResponse
    {
        try {

            return response()->json([
                "status" => true,
                "message" => "consumer updated successfully",
                "consumers" => $this->consumerRepository->update($request)
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in view of dashboard consumer' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Validate data .
     * @param $request
     * @return false|JsonResponse
     */
    private function validateData($request)
    {
        $validation['id'] = 'required|numeric|exists:users,id';
        if ($request->status) {
            $validation['status'] = 'required|numeric|in:0,1';
        }

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ValidationError::handle($validator);
        }
        return false;
    }

    public function sendEmail(EmailRequest $request)
    {
        try {


            $data['user_name'] = User::query()->where('email', $request->email)->first()->name;
            $data['subject'] = $request->subject;
            $data['message'] = $request->message;
            $this->consumerRepository->sendEmail($data);
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "email sent successfully",
                "data" => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {

            Log::error('error in send email of dashboard consumer' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
