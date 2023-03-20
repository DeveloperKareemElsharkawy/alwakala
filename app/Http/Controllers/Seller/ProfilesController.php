<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Actions\Action;
use App\Enums\Activity\Activities;
use App\Enums\Activity\ActivityType;
use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Enums\Views\AViews;
use App\Events\Store\VisitStore;
use App\Helpers\LogAction;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Requests\SellerApp\Auth\UpdateSellerInfoRequest;
use App\Http\Requests\SellerApp\Auth\UploadDocumentsRequest;
use App\Http\Requests\SellerApp\Store\ChangeMobileNumberRequest;
use App\Http\Requests\SellerApp\Store\ConfirmChangeMobileNumberRequest;
use App\Http\Requests\SellerApp\Store\ContactsRequest;
use App\Http\Requests\SellerApp\Store\UpdateStoreInfoRequest;
use App\Http\Requests\SellerApp\Store\UpdateStoreRequest;
use App\Http\Resources\Seller\AppTv\AppTvResource;
use App\Http\Resources\Seller\Categories\CategoriesResource;
use App\Http\Resources\Seller\Feeds\StoreHomeFeedResource;
use App\Http\Resources\Seller\Store\MyStoreConsumersCollection;
use App\Http\Resources\Seller\Store\SellerRateCollection;
use App\Http\Resources\Seller\Store\StoreOpeningHoursResource;
use App\Http\Resources\Seller\Store\StoreProfileResource;
use App\Http\Resources\Seller\StoreCollection;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Pagination\PaginationHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Helpers\Views\ViewsHelper;
use App\Lib\Log\ServerError;
use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\Store\DocumentResource;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\AppTv;
use App\Models\Category;
use App\Models\CategoryStore;
use App\Models\DaysOfWeek;
use App\Models\Feed;
use App\Models\FollowedStore;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\Seller;
use App\Models\SellerFavorite;
use App\Models\SellerRate;
use App\Models\Store;
use App\Models\StoreIdChanges;
use App\Models\StoreMobileChanges;
use App\Models\StoreOpeningHour;
use App\Models\User;
use App\Repositories\ActivitiesRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfilesController extends BaseController
{
    private $lang;
    public $productsRepo;
    public $profileRepo;
    public $storeRepository;
    private $storesRepo;

    public function __construct(Request $request, ProductRepository $productRepository, ProfileRepository $profileRepository, StoreRepository $storeRepository)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->productsRepo = $productRepository;
        $this->profileRepo = $profileRepository;
        $this->storesRepo = $storeRepository;

    }

    function ConfirmMobileCode(Request $request)
    {
        $rule = ['code' => 'required'];
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return ValidationError::handle($validator);
        }
        $object = Store::query()
            ->where('user_id', $request->user_id)
            ->where('confirm_code', $request->code)
            ->first();
        if (!$object) {
            return response()->json([
                'status' => false,
                'message' => trans('messages.auth.invalid_code'),
                'data' => ''
            ], AResponseStatusCode::BAD_REQUEST);
        }
        $object->confirm_code = "";
        $object->save();
        return response()->json([
            'status' => true,
            'message' => trans('messages.auth.valid_code'),
            'data' => []
        ], AResponseStatusCode::SUCCESS);
    }

    public function checkConfirmationAccount(Request $request)
    {
        try {
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_profile'),
                "data" => $this->profileRepo->checkConfirmationAccount($request->user_id)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in checkConfirmationAccount of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStoreConsumers(Request $request)
    {
        $orders = Order::query()->where('user_id', $request->user_id)->with('items')
            ->get();

        $request->merge(['where_stores_ids' => array_merge(...$orders->pluck('items.*.store_id')->toArray())]);
        $userId = UserId::UserId($request);
        $limit = 0;
        $pagination = 5;

        $consumers = $this->storesRepo->getStores($request, $userId, $limit, $pagination, null, null, false, false);

        return new  MyStoreConsumersCollection($consumers);
    }

    public function getProfile(Request $request)
    {
        try {
            $storeProfile = $this->profileRepo->getStoreProfile($request->user_id, $this->lang);
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_profile'),
                "data" => $storeProfile
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    // For visitors
    public function showProfile(Request $request, $storeId)
    {
        try {
            $userId = UserId::UserId($request);
            $store = Store::query()->find($storeId);
            if (!$store) {
                return response()->json([
                    "status" => false,
                    "message" => trans('messages.stores.store_not_found'),
                    "data" => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $storeProfile = $this->profileRepo->getStoreProfileForVisitors($userId, $this->lang, $storeId);
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_profile'),
                "data" => new StoreProfileResource($storeProfile)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateProfile(UpdateStoreRequest $request)
    {
        try {
            $store = Store::query()
                ->where('user_id', $request->user_id)
                ->first();
            $store->name = $request->name;
            $store->latitude = $request->latitude;
            $store->longitude = $request->longitude;
            $store->mobile = $request->mobile;
            $store->address = $request->address;
            $store->building_no = $request->building_no;
            $store->landmark = $request->landmark;
            $store->main_street = $request->main_street;
            $store->side_street = $request->side_street;
            $store->city_id = $request->city_id;
            $store->description = $request->description;
            $store->address = $request->address;
            $store->is_store_has_delivery = $request->is_store_has_delivery;
            $store->legal_name = $request->legal_name;

            if ($request->hasFile('logo')) {
                Storage::disk('s3')->delete($store->logo);
                $logo = $request->logo;
                $store->logo = UploadImage::uploadImageToStorage($logo, 'stores');
            }
            $store->save();

            $data['ref_id'] = $store->id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::UPDATE_STORE;
            $data['type'] = ActivityType::STORE;
            ActivitiesRepository::log($data);
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.profile_updated'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in updateProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function updateProfileInfo(UpdateStoreInfoRequest $request)
    {
        try {
            $store = Store::query()
                ->where('user_id', $request->user_id)->first();

            $validatedData = $request->validated();

            if ($request->hasFile('logo')) {
                Storage::disk('s3')->delete($store->logo);
                $validatedData['logo'] = UploadImage::uploadImageToStorage($request->logo, 'stores');
            }

            if ($request->seller_name) {
                unset($validatedData['seller_name']);
                User::query()->where('id', $request->user_id)->update(['name' => $request->seller_name]);
            }

            if ($request->store_profile_id) {
                StoreIdChanges::create(['store_id' => $store->id, 'old_store_profile_id' => $request->store_profile_id]);
            }

            $store->update($validatedData);

            if ($request->categories)
                $store->storeCategories()->sync($request->categories);

            $data['ref_id'] = $store->id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::UPDATE_STORE;
            $data['type'] = ActivityType::STORE;
            ActivitiesRepository::log($data);
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.profile_updated'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in updateProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return void
     */
    public function sendUpdateMobileCode(ChangeMobileNumberRequest $request)
    {
        $storeId = StoreId::getStoreID($request);

        $store = Store::find($storeId);

        if ($store->mobile == $request->mobile) {
            return $this->error(['message' => trans('messages.stores.choose_another_number')]);
        }

        $confirmCode = mt_rand(0, 8) . mt_rand(1, 9) . mt_rand(10, 90);

        StoreMobileChanges::query()->firstOrCreate(
            ['store_id' => $storeId, 'mobile' => $request->mobile, 'has_changed' => false],
            ['confirm_code' => $confirmCode]
        );

        return $this->success(['message' => 'Code Sent', 'data' => [
            'confirm_code' => $confirmCode
        ]]);
    }


    /**
     * @param ConfirmChangeMobileNumberRequest $request
     * @return void
     */
    public function updateMobileConfirmation(ConfirmChangeMobileNumberRequest $request)
    {
        $storeId = StoreId::getStoreID($request);

        Store::query()->where('id', $storeId)->update(['mobile' => $request->mobile]);

        StoreMobileChanges::query()->where([['store_id', $storeId], ['confirm_code', $request->confirm_code]])->update(['has_changed' => true]);

        return $this->success(['message' => 'Mobile Updated']);
    }

    public function updateFeedLink(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'feed_link' => 'nullable|string'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $store = Store::query()
                ->where('user_id', $request->user_id)
                ->first();
            $store->feed_link = $request->feed_link;
            $store->save();
            $data['ref_id'] = $store->id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::UPDATE_FEED_LINK;
            $data['type'] = ActivityType::STORE;
            ActivitiesRepository::log($data);
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.updated'),
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateFeedLink of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateStoreCategories(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'categories' => 'required|array',
                'categories.*' => 'required|numeric|exists:categories,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();
            $storeId = Store::query()->where('user_id', $request->user_id)
                ->first()->id;
            $categoryStore = new CategoryStore;

            $categoryStore->where('store_id', $storeId)->delete();
            $req = [];
            foreach ($request->categories as $k => $category) {
                $req[$k]['store_id'] = $storeId;
                $req[$k]['category_id'] = $category;
                $req[$k]['updated_at'] = Carbon::now();
                $req[$k]['created_at'] = Carbon::now();
            }
            $categoryStore->insert($req);
            $data['ref_id'] = $storeId;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::UPDATE_CATEGORIES;
            $data['type'] = ActivityType::STORE;
            ActivitiesRepository::log($data);
            DB::commit();
            return response()->json([
                "status" => true,
                "message" => trans('messages.general.success'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in updateStoreCategories of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeHome(GetProductsRequest $request, $storeId)
    {
        try {

            if (!Store::query()->where('id', $storeId)->first())
                return $this->error(['message' => trans('messages.stores.store_not_found')]);

            $userId = UserId::UserId($request);

            $categories = Category::query()
                ->select('categories.id', 'categories.name_' . $this->lang . ' as name', 'categories.image')
                ->join('category_store', 'categories.id', '=', 'category_store.category_id')
                ->where('store_id', $storeId)
                ->get();

            foreach ($categories as $category) {
                if ($category->image)
                    $category->image = config('filesystems.aws_base_url') . $category->image;
            }

            $slider = AppTvResource::collection(AppTv::query()->get());


            $following = false;

            if (!is_null($userId)) {
                $isFollow = FollowedStore::query()
                    ->where('store_id', $storeId)
                    ->where('user_id', $userId)
                    ->first();

                if ($isFollow)
                    $following = true;

            }

            $products = $this->newArrival($request, $storeId, $userId, true);

            $feeds = Feed::query()->with('store')->where('store_id', $storeId)->get();

            foreach ($feeds as $feed) {
                $feed['products'] = ProductStore::query()->where('store_id', $feed->store_id)
                    ->whereIn('product_id', $feed->products)->with('product.image')
                    ->get();
            }

            event(new VisitStore($request, $userId, $storeId));

            return $this->success(['message' => trans('messages.general.listed'),
                'data' => [
                    'feeds' => StoreHomeFeedResource::collection($feeds),
                    'categories' => $categories,
                    'slider' => $slider,
                    'hot_offers' => $products,
                    'best_selling' => $products,
                    'new_arrival' => $this->newArrival($request, $storeId, $userId, true),
                    'is_follow' => $following,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('error in storeHome of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeFeeds(Request $request, $storeId)
    {
        try {

            if (!Store::query()->where('id', $storeId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.stores.store_not_found'),
                    'data' => ''

                ], AResponseStatusCode::BAD_REQUEST);
            }
            $userId = UserId::UserId($request);
            $store = Store::query()->find($storeId);

            $productsIds = ProductStore::query()->where('store_id', $store->id)->pluck('product_id')->toArray();

            $query = Product::query()
                ->whereIn('products.id', $productsIds)
                ->with('productImage')
                ->leftJoin('product_store', 'products.id', '=', 'product_store.product_id')
                ->leftJoin('stores', 'product_store.store_id', '=', 'stores.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('seller_favorites', function ($join) use ($userId) {
                    $join->on('products.id', 'seller_favorites.favorited_id')
                        ->on('seller_favorites.store_id', 'stores.id')
                        ->where('favoriter_type', User::class)
                        ->where('favoriter_id', $userId)
                        ->where('favorited_type', Product::class);
                });
            $query->where('product_store.activation', '=', true);
            $query->select(
                [
                    'products.id',
                    'products.name as product_name',
                    'stores.id as store_id',
                    'stores.name as store_name',
                    'products.brand_id',
                    "brands.name_$this->lang as brand_name",
                    'product_store.discount',
                    'product_store.price',
                    'product_store.net_price',
                    'stores.name as store_name',
                    'products.created_at as created_at',
                    DB::raw('CASE WHEN COUNT(favorited_id) > 0 THEN true else false END as is_favorited')
                ])
                ->selectRaw("to_char(products.created_at, 'YYYY-MM-DD') as day")
                ->groupBy(['products.id', 'stores.id', 'brands.name_' . $this->lang, 'product_store.discount', 'product_store.net_price', 'product_store.price'])
                ->orderBy('products.created_at', 'desc');
            if ($request->filled('city_id')) {
                $query->where('stores.city_id', $request->query('city_id'));
            }
            if ($request->filled('brand_id')) {
                $query->where('products.brand_id', $request->query('brand_id'));
            }
            if ($request->filled('category_id')) {
                $query->where('products.category_id', $request->query('category_id'));
            }
            if ($request->filled('date')) {
                $query->orderBy('products.created_at', $request->query('date'));
            }
            if ($request->filled('price')) {
                $query->orderBy('price_range', $request->query('price'));
            }

            $products = $query->limit(10)->get()->groupBy('day');;
            $isActive = false;
            if ($userId) {
                $isActive = User::query()
                    ->select('activation')
                    ->where('id', $userId)
                    ->first()->activation;
            }

            $productsList = [];
            $index = 0;

            $lastFeedsDate = array_keys($products->toArray())[0] ?? '2021-01-01';

            $feedsTimeFilter = $lastFeedsDate > date("Y-m-d", strtotime("-7 day")) && count($products[$lastFeedsDate]) ? strtotime("-7 day") : strtotime("-90 day");

            foreach ($products as $date => $products) {


                if ($date > date("Y-m-d", $feedsTimeFilter)) {
                    $productsList[$index]['store_id'] = $storeId;
                    $productsList[$index]['store_name'] = $store->name;
                    $productsList[$index]['store_logo'] = $store->logo ? config('filesystems.aws_base_url') . $store->logo : null;
                    $productsList[$index]['store_views'] = ViewsHelper::getViewsCount($storeId, Store::class);
                    $productsList[$index]['date'] = $date;

                    foreach ($products as $product) {
                        $product->price = ProductHelper::canShowPrice($userId, $isActive, $product->price);
                        $product->net_price = ProductHelper::canShowPrice($userId, $isActive, $product->net_price);
                        if ($product->discount != 0 && $userId && $isActive) {
                            $product->has_discount = true;
//                        if ($product->discount_type == DiscountTypes::AMOUNT) {
//                            $product->discount_type = 'amount';
//                        } else {
                            $product->discount_type = 'percentage';
                            $product->discount = $product->discount . '%';
//                        }
                        } else {
                            $product->has_discount = false;
                        }
                        if (count($product->SellerRate) > 0) {
                            $product->rate = $product->SellerRate[0]->rate;
                        } else {
                            $product->rate = 0;
                        }
                        unset($product->SellerRate);
                        $product->image = null;
                        if ($product->productImage) {
                            $product->image = config('filesystems.aws_base_url') . $product->productImage->image;
                        }
                        unset($product->productImage);
                    }
                    $productsList[$index]['products'] = $products;
                    $index++;
                }
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.store_feeds'),
                'data' => [
                    'link' => Store::query()->where('id', $storeId)->select('feed_link')->first()->feed_link,
                    'products' => $productsList
                ]
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in storeFeeds of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeProducts(GetProductsRequest $request, $storeId)
    {
        try {


            $userId = UserId::UserId($request);
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = true;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);

            $productsIds = $products->pluck('id')->toArray();
            $categoriesIds = Product::query()->whereHas('productStore', function ($query) use ($productsIds) {
                $query->whereIn('product_id', $productsIds);
            })->pluck('category_id')->toArray();

            $categories = Category::query()
                ->whereIn('id', $categoriesIds)
                ->where('activation', true)
                ->get();

            $data = [
                'categories' => CategoriesResource::collection($categories),
                'products' => $products->items()
            ];

            return $this->respondWithPagination($products);
            return $this->respondPaginationWithAdditionalData($products, $data);
        } catch (\Exception $e) {
            Log::error('error in storeProducts of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeRates(Request $request, $storeId)
    {
        try {
            $storeRates = DB::select("SELECT
                        seller_rates.id,
                        seller_rates.rate,
                        seller_rates.review,
                        seller_rates.images,
                        seller_rates.created_at,
                        users.id as rater_id,
                        users.name as rated_by,
                        users.image as rater_image
                        From seller_rates
                        JOIN users on users.id = seller_rates.rater_id
                        WHERE rated_id = ?
                        AND rated_type = 'App\Models\Store'
                        ", [$storeId]);

            $storeRates = PaginationHelper::arrayPaginator($storeRates, $request, 10);

            return new SellerRateCollection($storeRates);

        } catch (\Exception $e) {
            Log::error('error in storeRates of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function addStoreCover(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cover' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2024',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $store = Store::query()
                ->where('user_id', $request->user_id)
                ->first();
            $store->cover = $request->cover;
            Storage::disk('s3')->delete($store->cover);
            $cover = $request->cover;
            $store->cover = UploadImage::uploadImageToStorage($cover, 'stores');

            $store->save();
            $data['ref_id'] = $store->id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::ADD_COVER_AREA;
            $data['type'] = ActivityType::STORE;
            ActivitiesRepository::log($data);
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_cover_added'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in addStoreCover of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    // to be delete
    public function addStoreRate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|numeric|exists:stores,id',
                'rate' => 'required|numeric|min:1|max:5',
                'review' => 'nullable|max:255',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $userId = UserId::UserId($request);


            SellerRate::updateOrCreate(
                ['rater_type' => User::class, 'rater_id' => $userId,
                    'rated_type' => Store::class, 'rated_id' => $request->store_id,],
                ['rate' => $request->rate, 'review' => $request->review]
            );
            $data['ref_id'] = $request->store_id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::ADD_RATE;
            $data['type'] = ActivityType::STORE;
            ActivitiesRepository::log($data);
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_rate_added'),
                "data" => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in addStoreRate of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function mostPopular($request, $storeId, $userId)
    {

        $arrayOfParameters['pagination'] = 0;
        $arrayOfParameters['limit'] = 5;
        $arrayOfParameters['isStoreProfile'] = true;
        $arrayOfParameters['request'] = $request;
        $arrayOfParameters['userId'] = $userId;
        $arrayOfParameters['storeId'] = $storeId;
        $products = $this->productsRepo->getProducts($arrayOfParameters);

    }

    private function newArrival(GetProductsRequest $request, $storeId, $userId, $isStoreProfile = false)
    {
        $arrayOfParameters['pagination'] = 0;
        $arrayOfParameters['limit'] = 5;
        $arrayOfParameters['isStoreProfile'] = $isStoreProfile;
        $arrayOfParameters['request'] = $request;
        $arrayOfParameters['userId'] = $userId;
        $arrayOfParameters['storeId'] = $storeId;
        return $this->productsRepo->getProducts($arrayOfParameters);

    }

    private function storeRatesLimited($storeId)
    {
        return DB::select("SELECT
                        seller_rates.rate,
                        seller_rates.review,
                        seller_rates.created_at,
                        users.name as rated_by
                        From seller_rates
                        JOIN users on users.id = seller_rates.rater_id
                        WHERE rated_id = ?
                        AND rated_type = 'App\Models\Store'
                        limit ?
                        ", [$storeId, 5]);
    }

    public function relatedProducts(GetProductsRequest $request)
    {
        try {
            $userId = UserId::UserId($request);
            $storeId = null;
            if ($userId) {
                $storeId = Store::query()
                    ->select('id')
                    ->where('user_id', $userId)
                    ->first()->id;
            }
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = true;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);
            return response()->json([
                'status' => true,
                'message' => trans('messages.product.products'),
                'data' => $products
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in relatedProducts of store products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function relatedProductsVistor($storeId, GetProductsRequest $request)
    {
        try {
            $userId = UserId::UserId($request);
            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = true;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);
            return response()->json([
                'status' => true,
                'message' => trans('messages.product.products'),
                'data' => $products
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in relatedProducts of store products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSellerLogos(Request $request)
    {
        try {
            $usersIds = UserRepository::getUsersIdsToStore($request);
            $logs = ActivitiesRepository::getLogs($usersIds);
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_logs'),
                "data" => $logs
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in get seller logos' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Upload documents for store.
     * @param UploadDocumentsRequest $request
     * @return JsonResponse
     */
    public function uploadStoreDocument(UploadDocumentsRequest $request): JsonResponse
    {
        try {
            if (!$this->profileRepo->uploadStoreDocument($request))
                return $this->error(['message' => trans('messages.auth.access_denied')]);


            return $this->success(['message' => trans('messages.stores.upload_documents')]);


        } catch (\Exception $e) {
            Log::error('error in upload store documents' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Upload documents for store.
     * @param getStoreDocument $request
     * @return JsonResponse
     */
    public function getStoreDocument(Request $request): JsonResponse
    {
        try {
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.upload_documents'),
                "data" => new DocumentResource($this->profileRepo->getStoreDocument($request)),
            ], AResponseStatusCode::CREATED);

        } catch (\Exception $e) {
            Log::error('error in upload store documents' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Upload documents for store.
     * @param Request $request
     * @return JsonResponse
     */
    public function getStoreStatus(Request $request): JsonResponse
    {
        try {
            $userId = UserId::UserId($request);
            $user = User::query()
                ->where('users.id', $userId)
                ->first();
            return response()->json([
                "status" => true,
                "message" => trans('messages.stores.store_status'),
                'data' => [
                    'activation' => $user->activation,
                ],
            ], AResponseStatusCode::CREATED);

        } catch (\Exception $e) {
            Log::error('error in getStoreStatus' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Upload documents for store.
     * @param Request $request
     * @return JsonResponse
     */
    public function syncContactsStores(ContactsRequest $request)
    {
        $storesIds = [];

        foreach ($request->contacts as $contact) {
            $user = User::query()->where('mobile', $contact)->first();

            if (!$user)
                break;

            $seller = Seller::query()->where('user_id', $user->id)->first();

            if (!$seller)
                break;

            $storesIds[] = $seller->store_id;
        }

        $request->merge(['where_stores_ids' => $storesIds]);
        $userId = UserId::UserId($request);
        $limit = 0;
        $pagination = 5;

        $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination, null, null, false, false);

        return new StoreCollection($stores);
    }
}
