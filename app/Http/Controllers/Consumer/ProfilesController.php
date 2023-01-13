<?php

namespace App\Http\Controllers\Consumer;


use App\Enums\Apps\AApps;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Store\VisitStore;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Consumer\Store\ReviewStoreRequest;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Resources\Consumer\Product\ProductResource;
use App\Http\Resources\Consumer\Store\ProfileResource;
use App\Http\Resources\Seller\AppTv\AppTvResource;
use App\Http\Resources\Seller\Feeds\FeedsCollection;
use App\Http\Resources\Seller\Feeds\FeedsResource;
use App\Http\Resources\Seller\Store\SellerRateResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Models\AppTv;
use App\Models\Category;
use App\Models\CategoryStore;
use App\Models\Feed;
use App\Models\FollowedStore;
use App\Models\ProductStore;
use App\Models\SellerFavorite;
use App\Models\SellerRate;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\StoreRepository;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ProfilesController extends BaseController
{
    public $productsRepo;
    public $profileRepo;
    private $lang;
    public $productService;

    public function __construct(ProductRepository $productRepository, ProductService $productService, ProfileRepository $profileRepository, Request $request)
    {
        $this->productsRepo = $productRepository;
        $this->profileRepo = $profileRepository;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->productService = $productService;

    }

    public function storeHomeOld(GetProductsRequest $request, $storeId)
    {
        try {
            if (!Store::query()->where('id', $storeId)->first()) {
                return $this->error(['message' => 'store not found']);
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


    public function storeHome(Request $request, $storeId)
    {
        try {

            // Store Information
            $store = Store::query()->with(['city.state.region.country', 'storeSettings', 'storeOpeningHours', 'owner'])
                ->with(array('SellerRate' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                    $query->take(3);
                }))
                ->withCount('products')->find($storeId);

            if (!$store) {
                return $this->error(['message' => 'Store not found']);
            }

            // Store Pinned Feed

            $feed = Feed::query()->with('store')->where('store_id', $storeId)->where('is_pinned', true)
                ->first();

            $feed['products'] = ProductStore::query()->where('store_id', $feed->store_id)
                ->whereIn('product_id', $feed->products)->with('product.image')
                ->get();


            // Store Products

            $hotOffers = $this->productService->productsByStore($storeId, $request, 3,['has_discount']);
            $newArrivals = $this->productService->productsByStore($storeId, $request, 3,['is_new_arrivals']);
            $bestSelling = $this->productService->productsByStore($storeId, $request, 3,['sort_by_most_selling']);


            return $this->success(['message' => '', 'data' => [
                'store_info' => new ProfileResource($store),
                'pinned_feed' => new FeedsResource($feed),
                'hot_offers' => ProductResource::collection($hotOffers),
                'new_arrivals' => ProductResource::collection($newArrivals),
                'best_selling' => ProductResource::collection($bestSelling),
            ]]);

        } catch (\Exception $e) {
            Log::error('error in showProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function showProfile(Request $request, $storeId)
    {
        try {
            $store = Store::query()->with(['city.state.region.country', 'storeSettings', 'storeOpeningHours', 'owner'])
                ->with(array('SellerRate' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                    $query->take(3);
                }))
                ->withCount('products')->find($storeId);

            if (!$store) {
                return $this->error(['message' => 'Store not found']);
            }

            return $this->success(['message' => '', 'data' => new ProfileResource($store)]);

        } catch (\Exception $e) {
            Log::error('error in showProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeReviews(Request $request, $storeId)
    {
        try {
            $store = Store::query()->with(['city.state.region.country', 'storeSettings', 'storeOpeningHours', 'owner'])
                ->with(array('SellerRate' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                    $query->take(3);
                }))
                ->withCount('products')->find($storeId);

            if (!$store) {
                return $this->error(['message' => 'Store not found']);
            }

            $reviews = SellerRate::where([['rated_type', Store::class], ['rated_id', $storeId]])->paginate(10);

            return $this->respondWithPagination(SellerRateResource::collection($reviews));

        } catch (\Exception $e) {
            Log::error('error in showProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeFeeds(Request $request, $storeId)
    {
        try {
            $feeds = Feed::query()->with('store')->where('store_id', $storeId)
                ->orderBy('is_pinned', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->paginate(10);

            foreach ($feeds as $feed) {
                $feed['products'] = ProductStore::query()->where('store_id', $feed->store_id)
                    ->whereIn('product_id', $feed->products)->with('product.image')
                    ->get();
            }

            return $this->respondWithPagination(FeedsResource::collection($feeds));

        } catch (\Exception $e) {
            Log::error('error in showProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeProducts(Request $request, $storeId)
    {
        try {
            $products = $this->productService->productsByStore($storeId, $request);

            return $this->respondWithPagination(ProductResource::collection($products));

        } catch (\Exception $e) {
            Log::error('error in showProfile of seller Profile ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function rateStore(ReviewStoreRequest $request)
    {
        try {

            SellerRate::updateOrCreate(
                ['rater_type' => User::class, 'rater_id' => $request->user_id,
                    'rated_type' => Store::class, 'rated_id' => $request->store_id,],
                ['rate' => $request->rate, 'review' => $request->review]
            );

            $store = Store::query()->with(['city.state.region.country', 'storeSettings', 'storeOpeningHours', 'owner'])
                ->with(array('SellerRate' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                    $query->take(3);
                }))
                ->withCount('products')->find($request->store_id);

            if (!$store) {
                return $this->error(['message' => 'Store not found']);
            }

            $reviews = SellerRate::where([['rated_type', Store::class], ['rated_id', $request->store_id]])->paginate(10);

            return $this->respondWithPagination(SellerRateResource::collection($reviews));

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
