<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\BaseController;
use App\Http\Requests\SellerApp\Cart\AddCartRequest;
use App\Http\Requests\SellerApp\Cart\ApplyCouponRequest;
use App\Http\Requests\SellerApp\Cart\ChangeCartQuantityRequest;
use App\Http\Requests\SellerApp\Cart\GetCartSummaryRequest;
use App\Http\Requests\SellerApp\Cart\RemoveCartRequest;
use App\Http\Resources\Seller\Cart\CartResource;
use App\Http\Resources\Seller\Cart\CartSummaryResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Coupon;
use App\Repositories\CartRepository;
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
    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $carts = $this->cartRepository->getCartsByUserId($request);
            return $this->success(['message' => trans('messages.general.listed'),
                'data' => [
                    'cart' => new CartResource($carts),
                    'recommended_products' => $this->cartRepository->recommendedProducts($request)
                ]
            ]);
        } catch (Exception $e) {
            Log::error('error in index of Seller Cart ' . __LINE__ . $e);
            return $e;
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
            return $this->success(['message' => trans('messages.general.listed'), 'data' => new CartSummaryResource($summary)]);
        } catch (Exception $e) {
            return $e;
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

            DB::commit();

            return $this->success(['message' => trans('messages.general.listed'), 'data' => [
                'cart' => new CartResource($carts),
                'recommended_products' => $this->cartRepository->recommendedProducts(request())
            ]]);
        } catch (Exception $e) {
            Log::error('error in store of Seller Cart' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param ApplyCouponRequest $request
     * @return JsonResponse
     */
    public function applyCoupon(ApplyCouponRequest $request)
    {
        $user_Cart = $this->cartRepository->getCartsByUserId(['user_id' => $request['user_id']]);
        $coupon = Coupon::where('code', $request->coupon_code)->first();

        try {
            if ($coupon->active == 0)
                return $this->error(['message' => trans('messages.cart.active')]);

            if (!Carbon::now()->between($coupon->start_date, $coupon->end_date))
                return $this->error(['message' => trans('messages.cart.apply')]);

            if ($coupon->quantity == 0)
                return $this->error(['message' => trans('messages.cart.quantity')]);

            if (!$user_Cart['cart_total'] >= $coupon->purchased_amount)
                return $this->error(['message' => trans('messages.cart.purchase_amount')]);

            $cart = $this->cartRepository->applyCoupon($request, $coupon, $user_Cart);


            return $this->success(['message' => trans('messages.general.listed'), 'data' => [
                'cart' => new CartResource($cart),
                'recommended_products' => $this->cartRepository->recommendedProducts(request())
            ]]);
        } catch (Exception $e) {
            Log::error('error in applying coupon in cart' . __LINE__ . $e);
            dd($e->getMessage());
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
            return $e;
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

}
