<?php

namespace App\Http\Controllers\Seller;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\SellerApp\AddAddressRequest;
use App\Http\Requests\SellerApp\DeleteAddressRequest;
use App\Http\Requests\SellerApp\EditAddressRequest;
use App\Http\Requests\SellerApp\Offer\AddOfferRequest;
use App\Http\Requests\SellerApp\Offer\ApproveOrRejectOfferRequest;
use App\Http\Requests\SellerApp\Offer\EditOfferRequest;
use App\Http\Requests\SellerApp\Offer\GetOfferRequest;
use App\Http\Resources\Seller\Offers\OfferResource;
use App\Http\Resources\Seller\Offers\OfferStoresStatusesResource;
use App\Http\Resources\Seller\Store\StoreMiniDataResource;
use App\Http\Resources\Seller\StoreCollection;
use App\Lib\Helpers\Authorization\AuthorizationHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use App\Models\Address;
use App\Models\Offer;
use App\Models\OfferNotification;
use App\Models\OfferProduct;
use App\Models\OfferStore;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\Store;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use App\Services\Offers\OffersService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OffersController extends BaseController
{

    private $lang;
    public $offersService;
    public $productRepository;
    public $storeRepository;

    public function __construct(Request $request, OffersService $offersService, ProductRepository $productRepository, StoreRepository $storeRepository)
    {
        // dd($request->all());
        $this->lang = LangHelper::getDefaultLang($request);
        $this->offersService = $offersService;
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
    }

    public function addOffer(AddOfferRequest $request)
    {
        try {

            $data = $request->validated();

            if ($data['discount_type'] == DiscountTypes::AMOUNT) {
                $data['discount_value'] = 0;
            } elseif ($data['discount_type'] == DiscountTypes::PERCENTAGE) {
                $data['bulk_price'] = 0;
                $data['retail_price'] = 0;
            }

            $myProductsWithOffers = Offer::query()->active()->where([
                ['user_id', $request->user_id],
                ['activation', true],
            ])->with('offers_products.product')->get();

            foreach ($myProductsWithOffers as $myProductsWithOffer) {
                foreach ($myProductsWithOffer->offers_products as $offerProduct) {
                    if (in_array($offerProduct->product_id, $request->products)) {
                        return $this->error(['message' => trans('messages.offers.offer_product_already_exist', ['name' => $offerProduct->product->name])]);
                    }
                }
            }

            $offer = $this->offersService->create($data);

            return response()->json([
                'status' => true,
                'message' => "Offer Create",
                'data' => new OfferResource($offer),
            ]);
        } catch (Exception $e) {
            Log::error('error in addOffer of seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public
    function editOffer(EditOfferRequest $request)
    {
        try {
            $data = $request->validated();
            $offer = $this->offersService->edit($data);
            return response()->json([
                'status' => true,
                'message' => "Offer Create",
                'data' => $offer,
            ]);
        } catch (Exception $e) {
            Log::error('error in addOffer of seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse
     */
    public
    function getOffers(Request $request)
    {
        try {
            return $this->respondWithPagination($this->offersService->list($request));

        } catch (Exception $e) {
            Log::error('error in getOffers of Seller' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @param $id
     * @return JsonResponse
     */
    public
    function getOffer(GetOfferRequest $request, $id)
    {
        try {
            $data = $this->offersService->show($request, $id);
            // dd($data);
            return response()->json([
                'status' => true,
                'message' => 'Offer',
                'data' => $data,
            ], AResponseStatusCode::SUCCESS);
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @param $offerId
     * @return JsonResponse
     */
    public
    function closeOffer(GetOfferRequest $request, $offerId)
    {
        try {
            $offer = Offer::query()->find($offerId);

            if ($offer->user_id != $request->user_id) {
                return $this->error(['message' => trans('messages.offers.offer_ownership_err')]);
            }

            if (!$offer->activation) {
                return $this->error(['message' => trans('messages.offers.offer_already_closed')]);
            }

            $offer->activation = false;
            $offer->save();

            return $this->success(['message' => trans('messages.offers.offer_closed')]);

        } catch (Exception $e) {
            Log::error('error in closeOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * delete offer and it's related pivot data
     *
     * @param GetOfferRequest $request
     * @param Int $id
     * @return JsonResponse
     */
    public
    function deleteOffer(GetOfferRequest $request, $id)
    {
        $data = $this->offersService->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'delete offer',
            'data' => []
        ], AResponseStatusCode::SUCCESS);
    }

    /**
     * @param GetOfferRequest $request
     * @param $offerId
     * @return JsonResponse
     */
    public
    function enrolledStores(Request $request, $offerId)
    {
        try {
            $enrolledStores = OfferStore::query()->with('store', 'offer')->where([['offer_id', $offerId], ['status', 'approved']])->paginate(10);

            return $this->respondWithPagination(OfferStoresStatusesResource::collection($enrolledStores));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @param $id
     * @return JsonResponse
     */
    public
    function rejectedStores(Request $request, $offerId)
    {
        try {
            $rejectedStores = OfferStore::query()->with('store', 'offer')->where([['offer_id', $offerId], ['status', 'rejected']])->paginate(10);

            return $this->respondWithPagination(OfferStoresStatusesResource::collection($rejectedStores));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @param $offerId
     * @return JsonResponse
     */
    public
    function allEnrolledStores(Request $request)
    {
        try {
            $offersIds = Offer::query()->where('user_id', $request->user_id)->pluck('id')->toArray();

            $enrolledStores = OfferStore::query()->with('store', 'offer')->whereIn('offer_id', $offersIds)->where('status', 'approved')->paginate(10);

            return $this->respondWithPagination(OfferStoresStatusesResource::collection($enrolledStores));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @param $offerId
     * @return JsonResponse
     */
    public
    function allRejectedStores(Request $request)
    {
        try {
            $offersIds = Offer::query()->where('user_id', $request->user_id)->pluck('id')->toArray();

            $enrolledStores = OfferStore::query()->with('store', 'offer')->whereIn('offer_id', $offersIds)->where('status', 'rejected')->paginate(10);

            return $this->respondWithPagination(OfferStoresStatusesResource::collection($enrolledStores));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }
    /**
     * @param GetOfferRequest $request
     * @param $offerId
     * @return JsonResponse
     */
    public
    function allPendingStores(Request $request)
    {
        try {
            $offersIds = Offer::query()->where('user_id', $request->user_id)->pluck('id')->toArray();

            $enrolledStores = OfferStore::query()->with('store', 'offer')->whereIn('offer_id', $offersIds)->where('status', 'pending')->paginate(10);

            return $this->respondWithPagination(OfferStoresStatusesResource::collection($enrolledStores));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @return JsonResponse
     */
    public
    function availableOffers(Request $request)
    {
        try {
            $storeId = StoreId::getStoreID($request);
            $storesIds = OfferStore::query()->where([
                ['store_id', $storeId],
                ['status', 'pending']
            ])->pluck('offer_id')->toArray();

            $offers = Offer::query()->whereIn('id', $storesIds)
                ->where('activation', true)->latest()->paginate();

            return $this->respondWithPagination(OfferResource::collection($offers));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param GetOfferRequest $request
     * @return JsonResponse
     */
    public
    function myOffers(Request $request)
    {
        try {
            $offers = Offer::query()->where('user_id', $request->user_id)->latest()->paginate();

            return $this->respondWithPagination(OfferResource::collection($offers));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @return JsonResponse
     */
    public
    function myOffersStatuses(Request $request)
    {
        try {

            $offersIds = Offer::query()->where('user_id', $request->user_id)->pluck('id')->toArray();

            $offersStores = OfferStore::query()->with('store', 'offer')->whereIn('offer_id', $offersIds)->paginate();

            return $this->respondWithPagination(OfferStoresStatusesResource::collection($offersStores));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @return JsonResponse
     */
    public
    function myActiveOffers(Request $request)
    {
        try {
            $storeId = StoreId::getStoreID($request);
            $storesIds = OfferStore::query()->where([['store_id', $storeId], ['status', 'approved']])
                ->pluck('offer_id')->toArray();

            $offers = Offer::query()->whereIn('id', $storesIds)
                ->whereDate('to', '>=', Carbon::today()->toDateString())->paginate();

            return $this->respondWithPagination(OfferResource::collection($offers));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @return JsonResponse
     */
    public
    function myRejectedOffers(Request $request)
    {
        try {
            $storeId = StoreId::getStoreID($request);
            $storesIds = OfferStore::query()->where([['store_id', $storeId], ['status', 'rejected']])
                ->pluck('offer_id')->toArray();

            $offers = Offer::query()->whereIn('id', $storesIds)->paginate();

            return $this->respondWithPagination(OfferResource::collection($offers));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetOfferRequest $request
     * @return JsonResponse
     */
    public
    function myEndedOffers(Request $request)
    {
        try {
            $storeId = StoreId::getStoreID($request);
            $storesIds = OfferStore::query()->where([['store_id', $storeId], ['status', 'approved']])
                ->pluck('offer_id')->toArray();

            $offers = Offer::query()->whereIn('id', $storesIds)->whereDate('to', '<=', Carbon::today()->toDateString())->paginate();

            return $this->respondWithPagination(OfferResource::collection($offers));
        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param ApproveOrRejectOfferRequest $request
     * @return JsonResponse
     */
    public
    function approveRejectOffer(ApproveOrRejectOfferRequest $request)
    {
        try {

            $storeId = StoreId::getStoreID($request);

            $offer = Store::query()->with('productStores')->find($storeId);

            $myProductsIds = $offer->productStores->pluck('product_id')->toArray();

            $offer = Offer::query()->find($request->offer_id);

            $offerStore = OfferStore::query()->where([['offer_id', $request->offer_id, ['store_id', $storeId]]])->first();

            $availableOfferProducts = OfferProduct::query()->where('offer_id', $request->offer_id)->whereIn('product_id', $myProductsIds)->get();

            if ($offer->user_id == $request->user_id) {
                return $this->error(['message' => trans('messages.offers.accept_your_offer_err')]);
            }

            if (!$offerStore) {
                return $this->error(['message' => trans('messages.offers.accept_not_your_offer_err')]);
            }

            if ($offerStore->status == 'approved') {
                return $this->error(['message' => trans('messages.offers.offer_already_approved')]);
            }

            if ($offerStore->status != 'pending') {
                return $this->error(['message' => trans('messages.offers.accept_not_your_offer_err')]);
            }

            $offerStore->status = $request->status;
            $offerStore->save();

            if ($offerStore->status == 'approved') {

                foreach ($availableOfferProducts as $availableOfferProduct) {
                    $productStore = ProductStore::query()->where([['product_id', $availableOfferProduct->product_id], ['store_id', $storeId]])->first();
                    $productNewPriceList = OffersHelper::getPriceFromStoreInventory($request, $offer->id, $availableOfferProduct->product_id);

                    $productStore->net_price = $productNewPriceList['new_supplier_price'];
                    $productStore->consumer_price = $productNewPriceList['new_consumer_price'];

                    $productStore->original_supplier_price = $productNewPriceList['original_supplier_price'];
                    $productStore->original_consumer_price = $productNewPriceList['original_consumer_price'];

                    $productStore->save();
                }
                return $this->success(['message' => trans('messages.offers.offer_approved_successfully')]);
            }

            return $this->success(['message' => trans('messages.offers.offer_rejected_successfully')]);

        } catch (Exception $e) {
            Log::error('error in getOffer of Seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
