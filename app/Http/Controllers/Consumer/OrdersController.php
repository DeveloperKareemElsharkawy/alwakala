<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\Orders\AOrders;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Stock\ATransactionTypes;
use App\Enums\UserTypes\UserType;
use App\Events\Inventory\StockMovement;
use App\Events\Order\PlaceOrder;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Consumer\Order\ReceiveConsumerOrder;
use App\Http\Requests\Consumer\Review\ReviewPurchasedProductRequest;
use App\Http\Requests\Orders\ApproveOrders;
use App\Http\Requests\Orders\ApproveOrRejectOrders;
use App\Http\Requests\Orders\CancelOrderProducts;
use App\Http\Requests\Orders\CancelOrders;
use App\Http\Requests\Orders\ReceiveOrders;
use App\Http\Requests\Orders\SendPurchasedProductToInventoryRequest;
use App\Http\Resources\Consumer\Order\OrderItemsResource;
use App\Http\Resources\Consumer\Order\OrderResource;
use App\Http\Resources\Seller\Orders\OrdersCollection;
use App\Lib\Helpers\Coupon\CouponService;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\SellerRate;
use App\Models\ShoppingCart;
use App\Models\Store;
use App\Models\User;
use App\Repositories\Consumer\ConsumerCartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShoppingCartRepository;
use App\Services\Orders\CreateConsumerOrderService;
use App\Services\Orders\CreateOrderService;
use App\Services\Orders\OrdersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrdersController extends BaseController
{
    public $shoppingCartRepo, $productRepo, $ordersService;
    private $lang;
    private $orderRepository;
    private ConsumerCartRepository $cartRepository;

    /**
     * OrdersController constructor.
     * @param ShoppingCartRepository $shoppingCartRepo
     * @param ProductRepository $productRepository
     * @param OrdersService $ordersService
     * @param OrderRepository $orderRepository
     * @param ConsumerCartRepository $cartRepository
     * @param Request $request
     */
    public function __construct(ShoppingCartRepository $shoppingCartRepo,
                                ProductRepository      $productRepository,
                                OrdersService          $ordersService,
                                OrderRepository        $orderRepository,
                                ConsumerCartRepository $cartRepository,
                                Request                $request)
    {
        $this->shoppingCartRepo = $shoppingCartRepo;
        $this->productRepo = $productRepository;
        $this->ordersService = $ordersService;
        $this->orderRepository = $orderRepository;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->cartRepository = $cartRepository;

    }

    /**
     * @param Request $request
     * @param CreateConsumerOrderService $createOrderService
     * @return JsonResponse
     */
    public function addOrder(Request $request, CreateConsumerOrderService $createOrderService)
    {
        try {
            $currentShoppingCarts = $this->cartRepository->getCartsByUserId($request);
            if (count($currentShoppingCarts['stores']) == 0)
                return $this->error(['message' => trans('messages.order.no_cart_available', [])]);

            DB::beginTransaction();
            $response = $createOrderService->createOrder($currentShoppingCarts, $request);
            if (!$response['status']) {
                DB::rollBack();
                return $this->error(['message' => $response['message'], []]);
            }

            DB::commit();

            return $this->success(['message' => trans('messages.order.add'), 'data' => []]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in addOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function index(Request $request)
    {
        try {

            $orders = Order::where('user_id', $request->user_id)
                ->when($request->status_id, function ($q) use ($request) {
                    $q->where('status_id', $request->status_id);
                })
                ->when($request->total_price, function ($q) use ($request) {
                    $q->where('total_price', $request->total_price);
                })
                ->with(
                    'status',
                    'order_address',
                    'items.product',
                    'items.store',
                    'items.status',
                    'items.color',
                    'items.size.sizeType',
                    'payment_method',
                    'order_address.city'
                )->latest()->paginate(10);

            return $this->respondWithPagination(OrderResource::collection($orders));
        } catch (\Exception $e) {
            Log::error('error in salesOrders of seller Order' . __LINE__ . $e);

            return $this->connectionError($e);
        }
    }

    public function showOrder($orderId, Request $request)
    {
        try {

            $order = Order::where('user_id', $request->user_id)
                ->when($request->status_id, function ($q) use ($request) {
                    $q->where('status_id', $request->status_id);
                })
                ->when($request->total_price, function ($q) use ($request) {
                    $q->where('total_price', $request->total_price);
                })
                ->with(
                    'status',
                    'order_address',
                    'items.product',
                    'items.store',
                    'items.status',
                    'items.color',
                    'items.size.sizeType',
                    'payment_method',
                    'order_address.city'
                )->find($orderId);

            if (!$order) {
                return $this->error(['message' => trans('messages.general.not_found')]);
            }

            return $this->success([
                'data' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('error in salesOrders of seller Order' . __LINE__ . $e);

            return $this->connectionError($e);
        }
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $order = $this->orderRepository->getOrderDetails($id, $this->lang, true);
            return response()->json([
                'status' => true,
                'message' => 'order',
                'data' => $order
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard orders' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CancelOrders $request
     * @return JsonResponse
     */
    public function cancelOrder(CancelOrders $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::query()->with('items')->find($request->id);

            if ($order->status_id == AOrders::CANCELED) {
                return $this->error(['message' => trans('messages.order.already_canceled')]);
            }

            if ($order->status_id != AOrders::ISSUED) {
                return $this->error(['message' => trans('messages.order.cannot_cancel')]);
            }

            $order->status_id = AOrders::CANCELED;
            $order->save();

            $product = OrderProduct::query()->where('order_id', $order->id)->first();
            $product->status_id = AOrders::CANCELED;
            $product->save();


            $this->productRepo->CancelOrderStock($order->items);
            DB::commit();

            return $this->success(['message' => trans('messages.order.canceled')]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in cancelOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CancelOrderProducts $request
     * @return JsonResponse
     */
    public function cancelOrderProduct(CancelOrderProducts $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::query()->with('items.product')->find($request->order_id);

            $orderProductsForCancellation = OrderProduct::query()->with('order')->whereIn('id', $request->order_products_ids)->get();

            $total_price = 0.0;

            foreach ($orderProductsForCancellation as $orderProduct) {

                if ($orderProduct->order && $orderProduct->order->user_id != $request->user_id) {
                    return $this->error(['message' => trans('messages.order.order_product_false_ownership')]);
                }

                if ($orderProduct->status_id != AOrders::ISSUED) {
                    return $this->error(['message' => trans('messages.order.cannot_cancel')]);
                }

                $orderProduct->status_id = AOrders::CANCELED; // Change status of OrderProduct to CANCELED
                $orderProduct->save();


                $order = Order::query()->find($orderProduct->order_id);

                $activeOrderProducts = $order->items->whereNotIn('status_id', [AOrders::REJECT, AOrders::CANCELED]);

                $total_price = $activeOrderProducts->sum('total_price');
                $order->total_price = $total_price;
                $order->save();

                if ($activeOrderProducts->count() == 0) {
                    $order->status_id = AOrders::CANCELED;
                    $order->save();
                }
            }

            $this->productRepo->CancelOrderStock($orderProductsForCancellation);


            if ($order->coupon_id) {

                $orderProducts = OrderProduct::query()->with('order')->where('order_id', $order->id)
                    ->whereIn('status_id', [AOrders::ISSUED, AOrders::IN_PROGRESS])->get();
                $couponCheck = CouponService::OrderProductsCheck($order->coupon_id, $orderProducts);

                if ($couponCheck['has_coupon']) {
                    $order->coupon_data = $couponCheck;
                    $order->total_price = round($total_price - $couponCheck['discountAmount'], 2);
                    $order->save();
                }
            }
            DB::commit();

            return $this->success(['message' => trans('messages.order.canceled'), 'data' => []]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in cancelOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param ReviewPurchasedProductRequest $request
     * @return JsonResponse
     */
    public function reviewPurchasedProduct(ReviewPurchasedProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $images = [];
            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $images[] = UploadImage::uploadImageToStorage($image, 'rates/store/' . $data['store_id']);
                }
                $data['images'] = $images;
            }

            SellerRate::updateOrCreate(
                ['rater_type' => User::class, 'rater_id' => $request->user_id,
                    'rated_type' => Product::class, 'rated_id' => $request->store_id,],
                ['rate' => $request->rate, 'review' => $request->review, 'images' => $images]
            );

            DB::commit();

            return $this->success(['message' => trans('messages.reviews.add'), 'data' => []]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in cancelOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param ReceiveOrders $request
     * @return JsonResponse
     */
    public function receiveOrder(ReceiveConsumerOrder $request)
    {
        try {
            DB::beginTransaction();

            $orderProducts = OrderProduct::query()->where('order_id', $request->order_id)->get();

            $products = $orderProducts->where('status_id', '!=', AOrders::SHIPPING);

            if (count($products)) {
                $notValidOrderedProductsIds = implode(' - ', $products->pluck('id')->toArray());
                return $this->error(['message' => trans('messages.order.order_products_status_not_valid', ['products' => $notValidOrderedProductsIds])]);
            }

            $orderProducts->toQuery()->update(['status_id' => AOrders::RECEIVED]);

            DB::commit();

            return $this->success(['message' => trans('messages.order.received'), 'data' => []]);

        } catch
        (\Exception $e) {
            DB::rollBack();
            Log::error('error in receiveOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }



}
