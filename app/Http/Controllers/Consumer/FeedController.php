<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\Activity\Activities;
use App\Enums\Activity\ActivityType;
use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Enums\Views\AViews;
use App\Events\Store\VisitStore;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Requests\SellerApp\Auth\UpdateSellerInfoRequest;
use App\Http\Requests\SellerApp\Auth\UploadDocumentsRequest;
use App\Http\Requests\SellerApp\Feeds\CreateFeedRequest;
use App\Http\Requests\SellerApp\Feeds\ToggleFavoriteFeedRequest;
use App\Http\Requests\SellerApp\Feeds\UpdateFeedRequest;
use App\Http\Requests\SellerApp\Store\ContactsRequest;
use App\Http\Requests\SellerApp\Store\ChangeMobileNumberRequest;
use App\Http\Resources\Seller\Feeds\FeedsCollection;
use App\Http\Resources\Seller\Feeds\FeedsResource;
use App\Http\Resources\Seller\Store\MyStoreConsumersCollection;
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
use App\Models\ProductImage;
use App\Models\ProductStore;
use App\Models\Seller;
use App\Models\SellerFavorite;
use App\Models\SellerRate;
use App\Models\Store;
use App\Models\StoreOpeningHour;
use App\Models\User;
use App\Repositories\ActivitiesRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FeedController extends BaseController
{
    public $productsRepo;
    public $profileRepo;
    public $storeRepository;

    public function __construct(ProductRepository $productRepository, ProfileRepository $profileRepository)
    {
        $this->productsRepo = $productRepository;
        $this->profileRepo = $profileRepository;

    }

    /**
     * @throws Exception
     */
    public function createFeed(CreateFeedRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->has('images')) {
                $images = [];
                foreach ($data['images'] as $image) {
                    $images[] = UploadImage::uploadImageToStorage($image, 'Feeds/store/' . $data['store_id']);
                }
                $data['images'] = $images;
            }

            $feed = Feed::query()->create($data);

            $feed['products'] = ProductStore::query()->where('store_id', $feed['store_id'])
                ->whereIn('product_id', $feed['products'])->with('product.image')->get();

            return $this->success(['message' => trans('messages.feed.created'), 'data' => new FeedsResource($feed)]);

        } catch (\Exception $e) {
            Log::error('error in Creating New Feed' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param Request $request
     * @return FeedsCollection|JsonResponse
     */
    public function allFeedsList(Request $request)
    {
        try {
            $feeds = Feed::query()->with('store')
                ->when($request->store_id, function ($q) use ($request) {
                    $q->where('store_id', $request->store_id);
                })
                ->paginate(10);

            foreach ($feeds as $feed) {
                $feed['products'] = ProductStore::query()->where('store_id', $feed->store_id)
                    ->whereIn('product_id', $feed->products)->with('product.image')->get();
            }

            return new FeedsCollection($feeds);

        } catch (\Exception $e) {
            Log::error('error in Creating New Feed' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $feedId
     * @param ToggleFavoriteFeedRequest $request
     * @return JsonResponse
     */
    public function getFeed($feedId, ToggleFavoriteFeedRequest $request)
    {
        try {
            $feed = Feed::query()->with('store')->find($feedId);

            $feed['products'] = ProductStore::query()->where('store_id', $feed['store_id'])
                ->whereIn('product_id', $feed['products'])->with('product.image')->get();

            return $this->success(['data' => new FeedsResource($feed)]);

        } catch (\Exception $e) {
            Log::error('error in Creating New Feed' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $feedId
     * @param ToggleFavoriteFeedRequest $request
     * @return JsonResponse
     */
    public function myFavorites(Request $request)
    {
        try {

            $feedsIds = SellerFavorite::query()
                ->where('favoriter_type', '=', User::class)
                ->where('favoriter_id', '=', $request->user_id)
                ->where('favorited_type', '=', Feed::class)
                ->pluck('favorited_id')->toArray();

            $feeds = Feed::query()->with('store')->whereIn('id', $feedsIds)->paginate();

            foreach ($feeds as $feed) {
                $feed['products'] = ProductStore::query()->where('store_id', $feed->store_id)
                    ->whereIn('product_id', $feed->products)->with('product.image')->get();
            }

            return new FeedsCollection($feeds);

        } catch (\Exception $e) {
            Log::error('error in Creating New Feed' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @throws Exception
     */
    public function updateFeed($feedId, UpdateFeedRequest $request)
    {
        try {

            $feed = Feed::query()->find($feedId);

            if (StoreId::getStoreID($request) != $feed['store_id']) {
                return $this->error(['message' => trans('messages.feed.unauthorized')]);
            }

            $data = $request->validated();

            if ($request->has('images')) {
                $images = [];
                foreach ($data['images'] as $image) {
                    $images[] = UploadImage::uploadImageToStorage($image, 'Feeds/store/' . $data['store_id']);
                }
                $data['images'] = $images;
            }

            $feed->update($data);

            $feed['products'] = ProductStore::query()->where('store_id', $feed['store_id'])
                ->whereIn('product_id', $feed['products'])->with('product.image')->get();

            return $this->success(['message' => trans('messages.feed.updated'), 'data' => new FeedsResource($feed)]);

        } catch (\Exception $e) {
            Log::error('error in Creating New Feed' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @throws Exception
     */
    public function toggleFavoriteFeed(ToggleFavoriteFeedRequest $request)
    {
        try {

             $findFavoritedProduct = SellerFavorite::query()
                ->where('favoriter_type', '=', User::class)
                ->where('favoriter_id', '=', $request->user_id)
                ->where('favorited_type', '=', Feed::class)
                ->where('favorited_id', '=', $request->feed_id)
                ->first();


            if (is_null($findFavoritedProduct)) {

                $favorite = new SellerFavorite();
                $favorite->favoriter_type = User::class;
                $favorite->favoriter_id = $request->user_id;
                $favorite->favorited_type = Feed::class;
                $favorite->favorited_id = $request->feed_id;
                $favorite->save();
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.feeds.favorite'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);

            } else {

                $findFavoritedProduct->delete();
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.feeds.unfavorite'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);

            }

        } catch (Exception $e) {
            Log::error('error in toggleFavoriteProduct of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
