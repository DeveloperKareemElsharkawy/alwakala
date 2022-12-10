<?php

namespace App\Http\Controllers\Consumer;


use App\Enums\Apps\AApps;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Store\VisitStore;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Resources\Seller\AppTv\AppTvResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Models\AppTv;
use App\Models\Category;
use App\Models\CategoryStore;
use App\Models\FollowedStore;
use App\Models\SellerFavorite;
use App\Models\SellerRate;
use App\Models\Store;
use App\Repositories\ProductRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ProfilesController extends BaseController
{
    public $productsRepo;
    public $profileRepo;
    private $lang;

    public function __construct(ProductRepository $productRepository, ProfileRepository $profileRepository, Request $request)
    {
        $this->productsRepo = $productRepository;
        $this->profileRepo = $profileRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function storeHome(GetProductsRequest $request, $storeId)
    {
        try {
            if (!Store::query()->where('id', $storeId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => 'store not found',
                    'data' => ''

                ], AResponseStatusCode::BAD_REQUEST);
            }
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
                    //->where('favoriter_type', User::class)
                    ->where('store_id', $storeId)
                    // ->where('favorited_type', Store::class)
                    ->where('user_id', $userId)
                    ->first();
                if ($isFollow) {
                    $following = true;
                } else {
                    $following = false;
                }
            } else {
                $following = false;
            }
            // todo fix call the same api with the same inputs !!
            $products = $this->newArrival($request, $storeId, $userId);
            $response = [
                'categories' => $categories,
                'slider' => $slider,
                'hot_offers' => $products,
                'best_selling' => $products,
                'new_arrival' => $this->newArrival($request, $storeId, $userId),
                'is_follow' => $following,
            ];
            event(new VisitStore($request, $userId, $storeId));
            return response()->json([
                'status' => true,
                'message' => 'Store Home',
                'data' => $response
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in storeHome of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function showProfile(Request $request, $storeId)
    {
        try {
            $userId = UserId::UserId($request);
            $store = Store::query()->find($storeId);
            if (!$store) {
                return response()->json([
                    "status" => false,
                    "message" => "store not found",
                    "data" => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $storeProfile = $this->profileRepo->getStoreProfileForVisitors($userId, $this->lang, $storeId);
            return response()->json([
                "status" => true,
                "message" => "store profile",
                "data" => $storeProfile
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function newArrival(GetProductsRequest $request, $storeId, $userId)
    {
        $arrayOfParameters['pagination'] = 0;
        $arrayOfParameters['limit'] = 5;
        $arrayOfParameters['isStoreProfile'] = true;
        $arrayOfParameters['request'] = $request;
        $arrayOfParameters['userId'] = $userId;
        $arrayOfParameters['storeId'] = $storeId;
        $arrayOfParameters['app'] = AApps::CONSUMER_APP;
        return $this->productsRepo->getProducts($arrayOfParameters);

    }
}
