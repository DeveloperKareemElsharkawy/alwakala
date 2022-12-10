<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Activity\Activities;
use App\Enums\Activity\ActivityType;
use App\Enums\Orders\AOrders;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Roles\ARoles;
use App\Enums\UserTypes\UserType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\SellerApp\Auth\ChangePasswordRequest;
use App\Http\Requests\SellerApp\Auth\RegisterSellerRequest;
use App\Http\Requests\SellerApp\Auth\SyncContactsRequest;
use App\Http\Requests\SellerApp\Auth\UpdateDeviceTokenRequest;
use App\Http\Requests\SellerApp\Auth\UpdatePasswordRequest;
use App\Http\Requests\SellerApp\Auth\validateFirstScreenRequest;
use App\Http\Requests\Shared\LoginRequest;
use App\Jobs\Emails\SendResetPasswordCodeJob;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Contract;
use App\Models\FollowedStore;
use App\Models\OauthAccessToken;
use App\Models\Order;
use App\Models\ProductStore;
use App\Models\Seller;
use App\Models\SettingUser;
use App\Models\Store;
use App\Models\User;
use App\Models\UserChangeCredential;
use App\Models\UserDeviceToken;
use App\Models\UserResetPassword;
use App\Repositories\ActivitiesRepository;
use App\Repositories\UserRepository;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class CredentialsController extends BaseController
{
    public $upload;
    private $lang;
    private $smsService;


    public function __construct(Request $request, UploadImage $upload, SmsService $smsService)
    {
        $this->smsService = $smsService;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->upload = $upload;
    }

    public function changeCredentials(Request $request)
    {
        try {
            $types = ['mobile', 'email'];
            if (!in_array($request->type, $types)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.type_error'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:mobile,email',
                'key' => 'required|string|unique:users,' . $request->type
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();
            $generateRandomCode = rand(1000, 9999);
            UserChangeCredential::query()->where('user_id', $request->user_id)->delete();
            UserChangeCredential::query()->create([
                'user_id' => $request->user_id,
                'confirm_code' => $generateRandomCode,
                'credential_type' => $request->type,
                'credential' => $request->key,
            ]);
            if ($request->type == 'email') {
                $user = User::find($request->user_id);
                $data = [
                    'user_name' => $user->name,
                    'reset_code' => $generateRandomCode,
                ];
                $job = (new SendResetPasswordCodeJob($data, $request->key))->delay(Carbon::now()->addSeconds(5));
                dispatch($job);
                $this->sendChangeEmailCodeToEmail($request);
            } elseif ($request->type == 'mobile') {
                $this->smsService->sendSmsToStoreChangeCredentials($request->key, $generateRandomCode);
            }

            Db::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.reset_code_sent'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in changeCredentials of seller auth' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function validateConfirmCode(Request $request)
    {
        try {
            $types = ['mobile', 'email'];
            if (!in_array($request->type, $types)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.type_error'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:mobile,email',
                'key' => 'required|string|unique:users,' . $request->type,
                'confirm_code' => 'required|string|max:4'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $user = UserChangeCredential::query()
                ->select('user_id')
                ->where('user_id', $request->user_id)
                ->where('confirm_code', $request->confirm_code)
                ->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.invalid_code'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $checkCode = UserChangeCredential::query()
                ->where('user_id', $user->user_id)
                ->where('credential_type', $request->type)
                ->where('credential', $request->key)
                ->where('confirm_code', $request->confirm_code)
                ->where('created_at', '>', Carbon::now()->subMinutes(5)->toDateTimeString())
                ->first();
            if (is_null($checkCode)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.expired_code'),
                    'data' => []
                ], AResponseStatusCode::FORBIDDEN);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.valid_code'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }
        } catch (\Exception $e) {
            Log::error('error in validateConfirmCode of seller auth' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function changeCredentialsSubmit(Request $request)
    {
        try {
            $types = ['mobile', 'email'];
            if (!in_array($request->type, $types)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.type_error'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:mobile,email',
                'key' => 'required|string|unique:users,' . $request->type,
                'confirm_code' => 'required|string|max:4'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $user = UserChangeCredential::query()
                ->select('user_id')
                ->where('user_id', $request->user_id)
                ->where('confirm_code', $request->confirm_code)
                ->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.invalid_code'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $checkCode = UserChangeCredential::query()
                ->where('user_id', $user->user_id)
                ->where('credential_type', $request->type)
                ->where('credential', $request->key)
                ->where('confirm_code', $request->confirm_code)
                ->where('created_at', '>', Carbon::now()->subMinutes(5)->toDateTimeString())
                ->first();
            if (is_null($checkCode)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.expired_code'),
                    'data' => []
                ], AResponseStatusCode::FORBIDDEN);
            } else {
                User::query()->where('id', $checkCode->user_id)
                    ->update([
                        $checkCode->credential_type => $checkCode->credential
                    ]);
                UserChangeCredential::query()->where('id', $checkCode->id)->delete();
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.'.$request->type.'_changed'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }
        } catch (\Exception $e) {
            Log::error('error in changePassword of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
