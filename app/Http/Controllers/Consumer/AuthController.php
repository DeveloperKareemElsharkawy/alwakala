<?php

namespace App\Http\Controllers\Consumer;


use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Consumer\RegisterConsumerRequest;
use App\Http\Requests\Shared\LoginRequest;
use App\Jobs\Emails\SendResetPasswordCodeJob;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\OauthAccessToken;
use App\Models\SettingUser;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Models\UserResetPassword;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{

    public $upload;

    public function __construct(UploadImage $upload)
    {
        $this->upload = $upload;
    }

    public function register(RegisterConsumerRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = new User();
            $data['type_id'] = UserType::CONSUMER;
            $user->initializeUserFields($data);
            $user->share_code = \Str::random(10).uniqid();
            $user->save();

            $settingUser = SettingUser::query()->create([
                'language' => 'en',
                'push_notification' => false,
                'user_id' => $user->id
            ]);
            $seller_response = [
                'id' => $user->id,
                'name' => $user->name,
                'user_id' => $user->user_id,
                'activation' => $user->activation,
                'setting' => $settingUser,
            ];
            $token = $user->createToken('myApp')->accessToken;
            DB::commit();

            $seller_response['token'] = $token;
            $this->sendResetCodeToMobile($data['mobile']);
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.user_created'),
                'data' => $seller_response
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('consumer register error' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateDeviceToken(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            UserDeviceToken::query()->updateOrCreate(
                ['user_id' => $request->user_id],
                ['token' => $request->token]
            );

            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.device_token_updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in updateDeviceToken of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function testPushNotification(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $userToken = UserDeviceToken::query()->where('user_id', '=', $request->user_id)->first();
//            $token = [];
//            $token[0] = $userToken->token;

            $pushNotificationObject = new \App\Lib\FCM\PushNotification();
            $response = $pushNotificationObject->PushNotification($userToken->token, 'Elwekala', 'hello from Elwekala');

            if ($response > 0) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.error'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.notification_sent'),
                    'data' => ''
                ], AResponseStatusCode::SUCCESS);
            }

        } catch (\Exception $e) {
            Log::error('error in testPushNotification of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function resetPassword(Request $request): \Illuminate\Http\JsonResponse
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
                'key' => 'required|string|exists:users,' . $request->type
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();

            if ($request->type == 'email') {
                $this->sendResetCodeToEmail($request->key);
            } elseif ('mobile') {
                $this->sendResetCodeToMobile($request->key);
            }

            //send the sms

            Db::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.reset_code_sent'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in resetPassword of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function validateConfirmCode(Request $request): \Illuminate\Http\JsonResponse
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
                'key' => 'required|string|exists:users,' . $request->type,
                'confirm_code' => 'required|string|max:4'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            // TODO this for test purpose will be removed
            if ($request->confirm_code == '0000') {
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.valid_code'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }

            $code = UserResetPassword::query()
                ->select('user_id')
                ->where('confirm_code', $request->confirm_code)
                ->first();
            if (is_null($code)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.invalid_code'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $checkCode = UserResetPassword::query()
                ->join('users', 'user_reset_passwords.user_id', '=', 'users.id')
                ->where('users.id', $code->user_id)
                ->where('users.' . $request->type, $request->key)
                ->where('user_reset_passwords.confirm_code', $request->confirm_code)
                ->where('user_reset_passwords.created_at', '>', Carbon::now()->subMinutes(1)->toDateTimeString())
                ->first();


            if (is_null($checkCode)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.expired_code'),
                    'data' => []
                ], AResponseStatusCode::FORBIDDEN);
            } else {
                $user = User::query()->where('id',$code->user_id)->first();
                $token = $user->createToken('myApp')->accessToken;
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.valid_code'),
                    'consumer' => $user,
                    'token'=>$token,
                ], AResponseStatusCode::SUCCESS);
            }

        } catch (\Exception $e) {
            Log::error('error in validateConfirmCode of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'key' => 'required|string|exists:users,' . $request->type,
                'confirm_code' => 'required|string|max:4',
                'password' => 'required|string|max:255'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            // TODO this for test purpose will be removed
            if ($request->confirm_code == '0000') {
                $user = User::query()
                    ->where($request->type, $request->key)
                    ->first();
                $user->activation = true;
                $user->save();
                UserResetPassword::query()->where('user_id', $user->id)->delete();
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.pass_changed'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }
            $user = UserResetPassword::query()
                ->select('user_id')
                ->where('confirm_code', $request->confirm_code)
                ->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.invalid_code'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $checkCode = UserResetPassword::query()
                ->join('users', 'user_reset_passwords.user_id', '=', 'users.id')
                ->where('users.id', $user->user_id)
                ->where('users.' . $request->type, $request->key)
                ->where('user_reset_passwords.confirm_code', $request->confirm_code)
               // ->where('user_reset_passwords.created_at', '>', Carbon::now()->subMinutes(1)->toDateTimeString())
                ->select(['user_reset_passwords.id', 'user_reset_passwords.user_id'])
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
                        'password' => bcrypt($request->password)
                    ]);

                UserResetPassword::query()->where('id', $checkCode->id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.pass_changed'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }
        } catch (\Exception $e) {
            Log::error('error in changePassword of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function sideData(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = User::query()
                ->select('id', 'email', 'name', 'image')
                ->where('id', $request->user_id)
                ->first();

            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.side_data'),
                'data' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'id' => $user->id,
                    'image' => $user->image,
                ]
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('sideData error ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function login(LoginRequest $request)
    {
         $key = (filter_var(request('email'), FILTER_VALIDATE_EMAIL)) ? 'email' : 'mobile';
        if (Auth::attempt([$key => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $token = $user->createToken('myApp')->accessToken;
            // TODO make it separated on other function
            /*  $body = [
                  'user_id' => $user->id,
              ];
              $client = new Client();
             // $res = $client->post(env('REALTIME_BASE_URL') . 'generate/token', $body);*/

            return response()->json([
                'status' => 200,
                'consumer' => $user,
                'token' => $token,
                //  'realtime_token' => $res
            ], 200);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'check your credentials'], 401);
        }
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric|exists:oauth_access_tokens,user_id'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.error'),
                    'data' => ''
                ], AResponseStatusCode::UNAUTHORIZED);
            }

            OauthAccessToken::query()
                ->where('user_id', $request->user_id)
                ->delete();

            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.logout'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in logout of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function sendResetCodeToMobile($mobile)
    {
        $user = User::query()->where('mobile', $mobile)->first();

        $generateRandomCode = rand(1000, 9999);

        UserResetPassword::query()->where('user_id', $user->id)->delete();

        UserResetPassword::query()->create([
            'user_id' => $user->id,
            'confirm_code' => $generateRandomCode
        ]);
    }

    private function sendResetCodeToEmail($email)
    {
        $user = User::query()->where('email', $email)->first();

        $generateRandomCode = rand(1000, 9999);

        UserResetPassword::query()->where('user_id', $user->id)->delete();

        UserResetPassword::query()->create([
            'user_id' => $user->id,
            'confirm_code' => $generateRandomCode
        ]);

        $data = [
            'user_name' => $user->name,
            'reset_code' => $generateRandomCode,
        ];

        $job = (new SendResetPasswordCodeJob($data, $email))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
    }

    public function activeConsumerAccount(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|string|exists:users,mobile',
                'confirm_code' => 'required|string|max:4'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            // TODO this for test purpose will be removed
            if ($request->confirm_code == '0000') {
                $user = User::query()
                    ->where('mobile', $request->mobile)
                    ->first();
                $user->activation = true;
                $user->save();
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.valid_code'),
                    'data' => []
                ], AResponseStatusCode::SUCCESS);
            }
            $user = UserResetPassword::query()
                ->select('user_id')
                ->where('confirm_code', $request->confirm_code)
                ->first();
            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.invalid_code'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $checkCode = UserResetPassword::query()
                ->join('users', 'user_reset_passwords.user_id', '=', 'users.id')
                ->where('users.id', $user->user_id)
                ->where('users.mobile', $request->mobile)
                ->where('user_reset_passwords.confirm_code', $request->confirm_code)
                ->where('user_reset_passwords.created_at', '>', Carbon::now()->subMinutes(1)->toDateTimeString())
                ->first();


            if (is_null($checkCode)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.expired_code'),
                    'data' => []
                ], AResponseStatusCode::FORBIDDEN);
            } else {
                $newUser = User::query()->where('id', $user->user_id)->first();
                $newUser->activation = true;
                $newUser->save();
                UserResetPassword::query()->where('user_id', $user->user_id)->delete();
                $token = $newUser->createToken('myApp')->accessToken;
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.valid_code'),
                    'consumer' => $newUser,
                    'token'=>$token,
                ], AResponseStatusCode::SUCCESS);
            }

        } catch (\Exception $e) {
            Log::error('error in validateConfirmCode of consumer auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function resendActivationCode(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|exists:users,mobile',
        ]);

        if ($validator->fails()) {
            return ValidationError::handle($validator);
        }
        $this->sendResetCodeToMobile($request->mobile);
        return response()->json([
            'status' => true,
            'message' => trans('messages.auth.reset_code_sent'),
            'data' => []
        ], AResponseStatusCode::SUCCESS);
    }
}
