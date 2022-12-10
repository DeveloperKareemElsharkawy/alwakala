<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\Apps\AApps;
use App\Enums\Offers\AOfferTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Lib\Helpers\UserId\UserId;
use App\Models\AppTv;
use App\Models\Brand;
use App\Models\HomeSection;
use App\Models\Offer;
use App\Models\Store;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends BaseController
{
    public $productsRepo;
    public $storesRepo;

    public function __construct(ProductRepository $productRepository, StoreRepository $storeRepository)
    {
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
    }

    public function home(GetProductsRequest $request)
    {
        try {
            $userId = UserId::UserId($request);
            $storeId = null;
            if ($userId) {
                $store = Store::query()
                    ->select('id')
                    ->where('user_id', $userId)
                    ->first();
                if ($store) {
                    $storeId = $store->id;
                }
            }
            //todo return retailers products and stores
            $response = [
                'slider' => $this->getAppTv(),
                'hot_offers' => $this->hotOffers($storeId, $userId),
                'discounts' => $this->discount($request, $storeId, $userId),
                'product_category' => $this->productCategory($request, $storeId, $userId),
                'global_collection' => $this->getSections(),
                'brands' => $this->brands($request, $userId),
                // 'feeds' =>ProductRepository::feeds($storeId),
            ];
            return response()->json([
                'status' => true,
                'message' => 'Home',
                'data' => $response
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in home of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    // Home sections
    private function getAppTv()
    {
        try {
            $today = Carbon::today();
            $appTvs = AppTv::query()
                ->whereDate('expiry_date', '>=', $today)
                ->whereNull('category_id')
                ->where('app_id', AApps::SELLER_APP)
                ->get();

            foreach ($appTvs as $appTv) {
                if ($appTv->image)
                    $appTv->image = config('filesystems.aws_base_url') . $appTv->image;
            }
            return $appTvs;
        } catch (\Exception $e) {
            Log::error('error in getApp of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function productCategory(GetProductsRequest $request, $storeId, $userId)
    {
        try {

            $arrayOfParameters['pagination'] = 0;
            $arrayOfParameters['limit'] = 5;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            return $this->productsRepo->getProducts($arrayOfParameters);
        } catch (\Exception $e) {
            Log::error('error in productCategory of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    private function discount(GetProductsRequest $request, $storeId, $userId)
    {
        try {
            $request['discount']=true;
            $arrayOfParameters['pagination'] = 0;
            $arrayOfParameters['limit'] = 5;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $arrayOfParameters['app'] = AApps::CONSUMER_APP;
            return $this->productsRepo->getProducts($arrayOfParameters);
        } catch (\Exception $e) {
            Log::error('error in discount of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }


    }

    private function getSections()
    {
        try {
            $sections = HomeSection::query()
                ->where('activation', true)
                ->get();
            foreach ($sections as $section) {
                if ($section->image)
                    $section->image = config('filesystems.aws_base_url') . $section->image;
            }
            return $sections;
        } catch (\Exception $e) {
            Log::error('error in getSections of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    private function brands($request, $userId)
    {
        try {
            $brands = Brand::query()
                ->select('id', 'name', 'image')
                ->where('activation', true)
                ->limit(5)->get();
            foreach ($brands as $brand) {
                if ($brand->image)
                    $brand->image = config('filesystems.aws_base_url') . $brand->image;
            }
            return $brands;
        } catch (\Exception $e) {
            Log::error('error in brands of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    private function hotOffers($request, $userId)
    {
        try {
            $offers = Offer::query()
                ->with('user')
                ->select('id', 'name', 'image', 'user_id')
                ->where('type_id', AOfferTypes::SUPPLIER_TO_SELLER)
                ->where('activation', true)
                ->whereDate('to', '>=', Carbon::now())
                ->get();

            foreach ($offers as $offer) {
                $offer->store_name = $offer->user->stores->name;
                unset($offer->user);
                if ($offer->image)
                    $offer->image = config('filesystems.aws_base_url') . $offer->image;
            }
            return $offers;
        } catch (\Exception $e) {
            Log::error('error in hotOffers of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }


    }

    public function feeds(Request $request)
    {
        try {

            return response()->json([
                'status' => true,
                'message' => 'Feeds List',
                'data' => ProductRepository::feeds($request->store_id, $request->page)
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in feeds of consumer  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
