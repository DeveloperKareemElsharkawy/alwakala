<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Apps\AApps;
use App\Enums\Offers\AOfferTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Resources\Seller\Slider\SlidesResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\AppTv;
use App\Models\Brand;
use App\Models\HomeSection;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HomeController extends BaseController
{
    public $lang;
    public $productsRepo;
    public $storesRepo;

    public function __construct(Request $request, ProductRepository $productRepository, StoreRepository $storeRepository)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
    }

    public function home(GetProductsRequest $request)
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
            $response = [
                'slider' => SlidesResource::collection($this->getAppTv()),
                'hot_offers' => $this->hotOffers($storeId, $userId),
                'discounts' => $this->discount($request, $storeId, $userId),
                'product_category' => $this->productCategory($request, $storeId, $userId),
                'global_collection' => $this->getSections(),
                'brands' => $this->brands($request, $userId),
                //  'feeds' => ProductRepository::feeds($storeId),
            ];
            return response()->json([
                'status' => true,
                'message' => trans('messages.sections.home'),
                'data' => $response
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in home of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function justForYou(GetProductsRequest $request)
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
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);

            return response()->json([
                'status' => true,
                'message' => trans('messages.sections.just_for_you'),
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return $e;
            Log::error('error in justForYou of seller  home' . __LINE__ . $e);
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
            return $this->productsRepo->getProducts($arrayOfParameters);
        } catch (\Exception $e) {
            return $e;
            Log::error('error in productCategory of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    private function discount(GetProductsRequest $request, $storeId, $userId)
    {
        try {
            $request['discount'] = true;
            $arrayOfParameters['pagination'] = 0;
            $arrayOfParameters['limit'] = 5;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;

            return $this->productsRepo->getProducts($arrayOfParameters);
        } catch (\Exception $e) {
            return $e;
            Log::error('error in discount of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }


    }

    private function getSections()
    {
        try {
            $sections = HomeSection::query()
                ->where('activation', true)
                ->select('id', "name_$this->lang as name", 'activation', 'image', 'created_at', 'updated_at')
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
                ->select('id', "name_$this->lang as name", 'image')
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
                'message' => trans('messages.sections.feeds_list'),
                'data' => ProductRepository::feeds($request->store_id, $request->page)
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in feeds of seller  home' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
