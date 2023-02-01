<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Consumer\Cart\AddCartRequest;
use App\Http\Requests\SellerApp\Cart\ApplyCouponRequest;
use App\Http\Requests\SellerApp\Cart\ChangeCartQuantityRequest;
use App\Http\Requests\SellerApp\Cart\GetCartSummaryRequest;
use App\Http\Requests\SellerApp\Cart\RemoveCartItemsByStore;
use App\Http\Requests\SellerApp\Cart\RemoveCartRequest;
use App\Http\Resources\Consumer\Cart\CartResource;
use App\Http\Resources\Consumer\Cart\ConsumerCartSummaryResource;
use App\Http\Resources\Consumer\Product\ProductResource;
use App\Http\Resources\Seller\Cart\CartSummaryResource;
use App\Lib\Helpers\Coupon\CouponService;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Coupon;
use App\Repositories\CartRepository;
use App\Repositories\Consumer\ConsumerCartRepository;
use App\Services\Product\ProductService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends BaseController
{

    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @param CartRepository $cartRepository
     */
    public $productService;

    public function __construct(ConsumerCartRepository $cartRepository, ProductService $productService,)
    {
        $this->cartRepository = $cartRepository;
        $this->productService = $productService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $carts = $this->cartRepository->getCartsByUserId($request);
            $suggestedProducts = $this->productService->suggestedProducts(null, null, 0, $request);

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => [
                    'cart' => new CartResource($carts),
                    'suggested_products' => ProductResource::collection($suggestedProducts),
                ]
            ]);
        } catch (Exception $e) {
            Log::error('error in index of Seller Cart ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCount(Request $request)
    {
        try {
            $count = $this->cartRepository->getCount($request);
            return $this->success(['message' => trans('messages.general.listed'), 'data' => ($count)]);
        } catch (Exception $e) {
            Log::error('error in index of Seller Cart ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param GetCartSummaryRequest $request
     * @return JsonResponse
     */
    public function summary(GetCartSummaryRequest $request)
    {
        try {
            $summary = $this->cartRepository->getCartsSummaryByUserId($request);
            return $this->success(['message' => trans('messages.general.listed'), 'data' => new ConsumerCartSummaryResource($summary)]);
        } catch (Exception $e) {
            Log::error('error in index of Seller Cart ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param AddCartRequest $request
     * @return JsonResponse
     */
    public function addCart(AddCartRequest $request)
    {
        try {
            DB::beginTransaction();

            $carts = $this->cartRepository->addCartItem($request);
            $suggestedProducts = $this->productService->suggestedProducts(null, null, 0, $request);

            DB::commit();

            return $this->success(['message' => trans('messages.general.listed'), 'data' => [
                'cart' => new CartResource($carts),
                'suggested_products' => ProductResource::collection($suggestedProducts),
            ]]);
        } catch (Exception $e) {
            Log::error('error in store of Seller Cart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param ApplyCouponRequest $request
     * @return JsonResponse
     */
    public function applyCoupon(ApplyCouponRequest $request)
    {
        try {
            $userCart = $this->cartRepository->getCartsByUserId($request);

            $applyCoupon = CouponService::ApplyCoupon($request->validated(), $request, $userCart['stores']);

            if (!$applyCoupon['status']) {
                return $this->error(['message' => $applyCoupon['message']]);
            }

            $carts = $this->cartRepository->getCartsByUserId($request);

            $suggestedProducts = $this->productService->suggestedProducts(null, null, 0, $request);

            return $this->success(['message' => trans('messages.general.listed'), 'data' => [
                'cart' => new CartResource($carts),
                'suggested_products' => ProductResource::collection($suggestedProducts),
            ]]);
        } catch (Exception $e) {
            Log::error('error in applying coupon in cart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param RemoveCartRequest $request
     * @return JsonResponse
     */
    public function removeCartCoupon(Request $request)
    {
        try {
            $carts = $this->cartRepository->removeCartCoupon($request);

            return $this->success(['message' => trans('messages.general.listed'), 'data' => [
                'cart' => new CartResource($carts),
                'recommended_products' => $this->cartRepository->recommendedProducts(request())
            ]]);
        } catch (Exception $e) {
            Log::error('error in applying coupon in cart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param ChangeCartQuantityRequest $request
     * @return JsonResponse
     */
    public function changeCartQuantity(ChangeCartQuantityRequest $request)
    {
        try {
            $carts = $this->cartRepository->changeCartItemQuantity($request);
            return $this->success(['message' => trans('messages.cart.quantity_updated'), 'data' => [
                'cart' => new CartResource($carts),
                'recommended_products' => $this->cartRepository->recommendedProducts(request())
            ]]);
        } catch (Exception $e) {
            Log::error('error in store of Seller Cart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param RemoveCartRequest $request
     * @return JsonResponse
     */
    public function removeCart(RemoveCartRequest $request)
    {
        try {
            $carts = $this->cartRepository->removeCartItem($request);

            return $this->success(['message' => trans('messages.cart.cart_deleted'), 'data' => [
                'cart' => new CartResource($carts),
                'recommended_products' => $this->cartRepository->recommendedProducts(request())
            ]]);
        } catch (Exception $e) {
            Log::error('error in store of Seller Cart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function emptyCart(Request $request)
    {
        try {
            $carts = $this->cartRepository->emptyCart($request);

            return $this->success(['message' => trans('messages.cart.cart_deleted'), 'data' => [
                'cart' => new CartResource($carts),
                'recommended_products' => $this->cartRepository->recommendedProducts(request())
            ]]);
        } catch (Exception $e) {
            Log::error('error in store of Seller Cart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param RemoveCartItemsByStore $request
     * @return JsonResponse
     */
    public function removeCartByStore(RemoveCartItemsByStore $request)
    {
        try {
            $carts = $this->cartRepository->removeCartsItemsByStore($request);

            return $this->success(['message' => trans('messages.cart.cart_deleted'), 'data' => [
                'cart' => new CartResource($carts),
                'recommended_products' => $this->cartRepository->recommendedProducts(request())
            ]]);
        } catch (Exception $e) {
            Log::error('error in store of Seller Cart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
