<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Activity\Activities;
use App\Enums\Activity\ActivityType;
use App\Enums\Orders\AOrders;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Roles\ARoles;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Events\Users\PendingForReview;
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
use App\Models\Coupon;
use App\Models\Feed;
use App\Models\FollowedStore;
use App\Models\OauthAccessToken;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductStore;
use App\Models\Seller;
use App\Models\Settings\SellerAppSettings;
use App\Models\SettingUser;
use App\Models\Store;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Models\UserResetPassword;
use App\Repositories\ActivitiesRepository;
use App\Repositories\UserRepository;
use App\Services\Mail\MailService;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;

class AuthController extends BaseController
{
    public $upload;
    private $lang;
    private $smsService;
    private $mailService;


    public function __construct(Request $request, UploadImage $upload, SmsService $smsService, MailService $mailService)
    {
        $this->smsService = $smsService;
        $this->mailService = $mailService;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->upload = $upload;
    }

    public function login(LoginRequest $request)
    {
        // dd($this->smsService->sendSms("01004504511",'اهلا بيك فى الوكالة أول ايكو سيستم للجملة والتجزئة'));
        // dd($this->smsService->sendSms("01552305252",'Welcome Elwekala'));
        // dd($this->smsService->sendSms("01270795090",'Welcome Elwekala'));
        // dd($this->smsService->sendSms("01000848650",'Welcome Elwekala'));
        // dd("monem");
        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        if (Auth::attempt([$loginType => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->type_id != UserType::SELLER) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.invalid_login_data'),
                    'data' => ''
                ], AResponseStatusCode::UNAUTHORIZED);
            }

            $seller = Seller::query()->where('user_id', $user->id)->first();
            $store = Store::query()
                ->select('id', 'name', 'user_id', 'store_type_id')
                ->where('id', $seller->store_id)
                ->first();
            if (!$store) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => trans('messages.auth.no_store'),
                        'data', ''
                    ],
                    AResponseStatusCode::UNAUTHORIZED
                );
            }

            //            // TODO make it separated on other function
            //            $body = [
            //                'store_id' => $store->id,
            //                'user_id' => $user->id,
            //            ];
            //            $realtimeResponse = Http::post(env('REALTIME_BASE_URL') . '/generate/token', $body);

            $token = $user->createToken('myApp')->accessToken;
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.login'),
                'data' => [
                    'store_id' => $store->id,
                    'store_name' => $store->name,
                    'store_type_id' => $store->store_type_id,
                    'activation' => $user->activation,
                    'token' => $token,
                    //                    'realtime_token' => $realtimeResponse['token']
                ]
            ], AResponseStatusCode::SUCCESS);
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('messages.auth.invalid_login_data'),
                'data' => ''
            ], AResponseStatusCode::UNAUTHORIZED);
        }
    }

    public function logout(Request $request)
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

    public function register(RegisterSellerRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = new User();
            $data['type_id'] = UserType::SELLER;
            $user->initializeUserFields($data);
            $user->save();

            $token = $user->createToken('myApp')->accessToken;
            $store = new Store();
            $store->user_id = $user->id;
            $store->name = $request->store_name;
            $store->store_type_id = $request->store_type_id;
            $store->latitude = $request->latitude;
            $store->longitude = $request->longitude;
            $store->mobile = $request->store_mobile;
            $store->address = $request->address;
            $store->building_no = $request->building_no;
            $store->landmark = $request->landmark;
            $store->main_street = $request->main_street;
            $store->side_street = $request->side_street;
            $store->city_id = $request->city_id;
            $store->legal_name = $request->legal_name;
            $store->description = $request->description;
            // $store->is_store_has_delivery = $request->is_store_has_delivery;
            if ($request->logo) {
                $store->logo = UploadImage::uploadImageToStorage($request->logo, 'stores');
            }
            if ($request->licence) {
                $store->licence = UploadImage::uploadImageToStorage($request->licence, 'stores');
            }
            $store->confirm_code = rand(1000, 9999);

            $generatedQRCode = 'st-' . mt_rand(1000000, 9999999); //  generate qrcode
            $image = \QrCode::format('png')
                ->size(200)->errorCorrection('H')
                ->generate($generatedQRCode); //   generate qrcode image

            $uploadedImage = UploadImage::uploadSVGToStorage($image); // upload qrcode image to s3 storage

            $store->qr_code_image = $uploadedImage;
            $store->qr_code = $generatedQRCode;

            if ($request->main_branch_store_id) {
                $store->parent_id = $request->main_branch_store_id;
                $store->is_main_branch = false;
            }

            $store->save();

            $this->smsService->sendSmsToStoreRegister($user->mobile, $store->confirm_code, $request->user_lang);
            if ($user->email)
                $this->mailService->sendMailToStoreRegister($user->email, $user->name, $store->confirm_code, $request->user_lang);
            $seller = new Seller();
            $seller->user_id = $user->id;
            $seller->store_id = $store->id;
            $seller->role_id = UserRepository::getUserRoleByName(ARoles::OWNER, UserType::SELLER);
            $seller->save();

            $store->brands()->attach($request->brands);
            $store->storeCategories()->attach($request->store_categories);
            SettingUser::query()->create([
                'language' => $request->user_lang,
                'push_notification' => false,
                'user_id' => $user->id
            ]);
            $data['ref_id'] = $store->id;
            $data['user_id'] = $user->id;
            $data['action'] = Activities::CREATE_STORE;
            $data['type'] = ActivityType::STORE;
            ActivitiesRepository::log($data);


            UserDeviceToken::updateOrCreate(
                ['user_id' => $seller->user_id],
                ['token' => $request->token]
            );
            event(new PendingForReview([$seller->user_id]));

            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.store_created'),
                'data' => [
                    'store_type_id' => $store->store_type_id,
                    'activation' => $user->activation,
                    'token' => $token,
                    'confirm_code' => $store->confirm_code
                ]
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in register of seller auth' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function validateFirstScreen(validateFirstScreenRequest $request)
    {
        try {
            return response()->json([
                "status" => true,
                "message" => trans('messages.auth.register_step1'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in validateFirstScreen of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateDeviceToken(UpdateDeviceTokenRequest $request)
    {
        try {
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

    public function testPushNotificationOld(Request $request)
    {
        try {
            // dd("monem");
            $notificationBuilder = new PayloadNotificationBuilder('my title');
            $notificationBuilder->setBody('Hello world');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['type_id' => '7']);
            $dataBuilder->addData(['product_id' => '7000']);
            $data = $dataBuilder->build();

            $notification = $notificationBuilder->build();

            $topic = new Topics();
            $topic->topic('All');

            $topicResponse = FCM::sendToTopic($topic, null, $notification, $data);

            $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();


            return response()->json([
                'status' => false,
                'message' => trans('messages.auth.error'),
                'isSuccess' => $topicResponse->isSuccess(),
                'shouldRetry' => $topicResponse->shouldRetry(),
                'error' => $topicResponse->error()
            ], AResponseStatusCode::BAD_REQUEST);

        } catch (\Exception $e) {
            Log::error('error in testPushNotification of seller auth' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function testPushNotification(Request $request)
    {
        try {
            $userToken = UserDeviceToken::query()->where('user_id', '=', $request->user_id)->first();
            //            $token = [];
            //            $token[0] = $userToken->token;
            // if( $request->userId){}
            // dd( $request->user_id);
            // dd($userToken);

            $pushNotificationObject = new \App\Lib\FCM\PushNotification();
            $response = $pushNotificationObject->PushNotification($userToken->token, 'Elwekala', 'hello monem type 1 from Elwekala');

            if ($response > 0) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.error'),
                    'data' => $response
                ], AResponseStatusCode::BAD_REQUEST);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.auth.notification_sent'),
                    'data' => $response
                ], AResponseStatusCode::SUCCESS);
            }
        } catch (\Exception $e) {
            Log::error('error in testPushNotification of seller auth' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            if (filter_var(request()['email'], FILTER_VALIDATE_EMAIL)) {
                $type = 'email';
            } else {
                $type = 'mobile';
            }

            $types = ['mobile', 'email'];
            if (!in_array($type, $types)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.type_error'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|exists:users,' . $type
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            DB::beginTransaction();

            if ($type == 'email') {
                $code = $this->sendResetCodeToEmail($request->email);
            } elseif ($type == 'mobile') {
                $code = $this->sendResetCodeToMobile($request->email);
            }

            Db::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.reset_code_sent'),
                'data' => [
                    'confirm_code' => $code
                ]
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in resetPassword of seller auth' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function validateConfirmCode(Request $request)
    {
        try {

            if (filter_var(request()['email'], FILTER_VALIDATE_EMAIL)) {
                $type = 'email';
            } else {
                $type = 'mobile';
            }

            $types = ['mobile', 'email'];
            if (!in_array($type, $types)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.auth.type_error'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $validator = Validator::make($request->all(), [
                'key' => 'required|string|exists:users,' . $type,
                'confirm_code' => 'required|string|max:4'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
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
                ->where('users.' . $type, $request->key)
                ->where('user_reset_passwords.confirm_code', $request->confirm_code)
                ->where('user_reset_passwords.created_at', '>', Carbon::now()->subMinutes(5)->toDateTimeString())
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
            return $this->connectionError($e);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
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
                ->where('user_reset_passwords.created_at', '>', Carbon::now()->subMinutes(5)->toDateTimeString())
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

    public function sideData(Request $request)
    {
        try {
            $user = User::query()
                ->select('id', 'email', 'name', 'image')
                ->withCount('favorites')
                ->where('id', $request->user_id)
                ->first();

            $store = Store::query()
                ->select(
                    'stores.id',
                    'stores.name',
                    'stores.logo',
                    'stores.cover',
                    'stores.qr_code_image',
                    'city_id as city_id',
                    'cities.name_' . $this->lang . ' as city_name',
                    'states.id as state_id',
                    'states.name_' . $this->lang . ' as state_name',
                    'countries.id as country_id',
                    'countries.name_' . $this->lang . ' as country_name'
                )
                ->where('stores.user_id', $request->user_id)
                ->join('cities', 'cities.id', '=', 'stores.city_id')
                ->join('states', 'states.id', '=', 'cities.state_id')
                ->join('regions', 'regions.id', '=', 'states.region_id')
                ->join('countries', 'countries.id', '=', 'regions.country_id')
                ->first();

            $store->logo = config('filesystems.aws_base_url') . $store->logo;
            $store->cover = config('filesystems.aws_base_url') . $store->cover;
            $store->qr_code_image = config('filesystems.aws_base_url') . $store->qr_code_image;

            $followersCount = FollowedStore::query()->where('store_id', $store->id)->count();
            $followingCount = FollowedStore::query()->where('user_id', $user->id)->count();

            $productCount = ProductStore::query()->whereHas('product', function ($q) {
                $q->where('reviewed', true);
            })->where('store_id', $store->id)
                ->distinct('product_id')
                ->count('product_id');

            // Getting Consumers Data
            $consumers = Order::query()->where('user_id', $user->id)->with('items')
                ->get();

            $consumersCount = Store::query()->whereIn('id', array_merge(...$consumers->pluck('items.*.store_id')->toArray()))->count();

            // Getting Orders Data
            $orders = Order::query()->where('user_id', $request->user_id)->get();
            $ordersIds = $orders->pluck('id')->toArray();

            // Getting Suppliers Orders
            $suppliersOrders = OrderProduct::query()->whereHas('store', function ($q) {
                $q->where('store_type_id', StoreType::SUPPLIER);
            });

            // Getting Retailers Orders
            $retailersOrders = OrderProduct::query()->whereHas('store', function ($q) {
                $q->where('store_type_id', StoreType::RETAILER);
            });

            // Getting Purchase Orders
            $purchaseOrders = OrderProduct::query()->where('store_id', $store->id);

            // Settings
            $settings = app(SellerAppSettings::class);

            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.side_data'),
                'data' => [
                    'email' => $user->email,
                    'seller_name' => $user->name,
                    'store_name' => $store->name,
                    'store_id' => $store->id,
                    'store_logo' => $store->logo,
                    'store_cover' => $store->cover,
                    'user_image' => $user->image ? config('filesystems.aws_base_url') . $user->image : null,
                    'store_qr_code_image' => $store->qr_code_image,
                    'city_id' => $store->city_id,
                    'city_name' => $store->city_name,
                    'state_id' => $store->state_id,
                    'state_name' => $store->state_name,
                    'country_id' => $store->country_id,
                    'country_name' => $store->country_name,

                    'basic_counters' => [
                        'total_inventory' => $productCount,
                        'favorite_products_count' => $user->favorites_count,
                        'following_stores_count' => $followingCount,
                        'my_store_followers_count' => $followersCount,
                        'consumers_count' => $consumersCount,
                    ],

                    'suppliers_orders' => [
                        'pending' => $suppliersOrders->where('status_id', AOrders::ISSUED)->count(),
                        'in_progress' => $suppliersOrders->where('status_id', AOrders::IN_PROGRESS)->count(),
                        'delivered' => $suppliersOrders->where('status_id', AOrders::RECEIVED)->count(),
                        'canceled' => $suppliersOrders->where('status_id', AOrders::CANCELED)->count(),
                        'rejected' => $suppliersOrders->where('status_id', AOrders::REJECT)->count(),
                    ],

                    'retailers_orders' => [
                        'pending' => $retailersOrders->where('status_id', AOrders::ISSUED)->count(),
                        'in_progress' => $retailersOrders->where('status_id', AOrders::IN_PROGRESS)->count(),
                        'delivered' => $retailersOrders->where('status_id', AOrders::RECEIVED)->count(),
                        'canceled' => $retailersOrders->where('status_id', AOrders::CANCELED)->count(),
                        'rejected' => $retailersOrders->where('status_id', AOrders::REJECT)->count(),
                    ],

                    'purchase_orders' => [
                        'pending' => $purchaseOrders->where('status_id', AOrders::ISSUED)->count(),
                        'in_progress' => $purchaseOrders->where('status_id', AOrders::IN_PROGRESS)->count(),
                        'delivered' => $purchaseOrders->where('status_id', AOrders::RECEIVED)->count(),
                        'canceled' => $purchaseOrders->where('status_id', AOrders::CANCELED)->count(),
                        'rejected' => $purchaseOrders->where('status_id', AOrders::REJECT)->count(),
                    ],

                    'inventory_control' => [
                        'total' => $productCount,
                        'feeds_count' => Feed::query()->where('store_id', $store->id)->count(),
                        'coupon_count' => Coupon::query()->where('seller_id', $request->user_id)->count(),
                        'offers_count' => Offer::query()->where('user_id', $request->user_id)->count(),
                    ],

                    'social_links' => [
                        'facebook_url' => $settings->facebook_url,
                        'instagram_url' => $settings->instagram_url,
                        'twitter_url' => $settings->twitter_url,
                        'linkedin_url' => $settings->linkedin_url,
                        'website_url' => $settings->website_url,
                        'pinterest_url' => $settings->pinterest_url,
                        'youtube_url' => $settings->youtube_url
                    ],
                ]
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in sideData of seller auth' . __LINE__ . $e);
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
        $this->smsService->sendSmsToStoreForgetPassword($mobile, $generateRandomCode);
        return $generateRandomCode;
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

        $job = (new SendResetPasswordCodeJob($data, $email))->delay(Carbon::now()->addSeconds(1));
        dispatch($job);
        return $generateRandomCode;

    }

    public function getAgreement($type, $app)
    {
        try {
            $operation = '=';
            if ($type != 'faq') {
                $operation = '!=';
            }
            $agreement = Contract::query()
                ->select('id', 'name', 'agreement')
                ->where('name', $operation, 'F.A.Q')
                ->where('app_id', $app)
                ->get();

            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.agreement'),
                'data' => $agreement
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getAgreement of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            User::query()->where('id', $request->user('api')->id)
                ->update(['password' => bcrypt($request->new_password)]);
            OauthAccessToken::query()
                ->where('user_id', $request->user('api')->id)
                ->delete();
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.pass_changed'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updatePassword of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function syncContacts(SyncContactsRequest $request)
    {
        try {

            $numbers = [];
            foreach ($request->contacts as $contact) {
                $numbers[] = $contact['mobile'];
            }
            $userId = $request->user_id;
            $users = User::query()
                ->whereIn('mobile', $numbers)
                ->where('type_id', UserType::SELLER)
                ->pluck('mobile')
                ->toArray();
            $others = array_diff($numbers, $users);
            $stores = Store::query()
                ->select(
                    'stores.name',
                    'users.mobile',
                    DB::raw('CASE WHEN COUNT(followed_stores.store_id) > 0 THEN true else false END as is_followed')
                )
                ->join('users', 'users.id', '=', 'stores.user_id')
                ->leftJoin('followed_stores', function ($join) use ($userId) {
                    $join->on('stores.id', 'followed_stores.store_id')
                        ->where('followed_stores.user_id', $userId);
                })
                ->whereIn('users.mobile', $users)
                ->groupBy('stores.name', 'users.mobile')
                ->get()->toArray();

            $contacts = $request->contacts;
            $otherUsers = [];
            foreach ($contacts as $c) {
                if (in_array($c['mobile'], $others)) {
                    $otherUsers[] = $c;
                }
            }
            $response = array_merge($otherUsers, $stores);
            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.store_list'),
                'data' => $response
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updatePassword of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function checkToken(Request $request)
    {
        try {
            $isValid = Auth::guard('api')->check();
            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.store_list'),
                'data' => $isValid
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in check token' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
