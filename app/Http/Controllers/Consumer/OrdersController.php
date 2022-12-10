<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\Orders\AOrders;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Stock\ATransactionTypes;
use App\Enums\UserTypes\UserType;
use App\Events\Inventory\StockMovement;
use App\Events\Order\PlaceOrder;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Orders\ApproveOrders;
use App\Http\Requests\Orders\ApproveOrRejectOrders;
use App\Http\Resources\Seller\Orders\OrdersCollection;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ParentOrder;
use App\Models\ShoppingCart;
use App\Models\Store;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShoppingCartRepository;
use App\Services\Orders\OrdersService;
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

    /**
     * OrdersController constructor.
     * @param ShoppingCartRepository $shoppingCartRepo
     * @param ProductRepository $productRepository
     * @param OrdersService $ordersService
     * @param OrderRepository $orderRepository
     * @param Request $request
     */
    public function __construct(ShoppingCartRepository $shoppingCartRepo,
                                ProductRepository $productRepository,
                                OrdersService $ordersService,
                                OrderRepository $orderRepository,
                                Request $request)
    {
        $this->shoppingCartRepo = $shoppingCartRepo;
        $this->productRepo = $productRepository;
        $this->ordersService = $ordersService;
        $this->orderRepository = $orderRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrder(Request $request)
    {
        try {
            $currentShoppingCarts = $this->shoppingCartRepo->getCurrentShoppingCarts($request->user_id);
            if (count($currentShoppingCarts) == 0) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.order.no_cart_available'),
                    'data' => [],
                ], AResponseStatusCode::BAD_REQUEST);
            }
            DB::beginTransaction();
            $response = $this->ordersService->insertOrderData($currentShoppingCarts);
            if (!$response['status']) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => [],
                ], AResponseStatusCode::BAD_REQUEST);
            }
            DB::commit();
            foreach ($currentShoppingCarts as $ShoppingCart) {
                foreach ($ShoppingCart->products as $storeProduct) {
                    event(new StockMovement($storeProduct['pivot']['purchased_item_count'], $storeProduct['pivot']['product_id'], ATransactionTypes::PURCHASE_ORDER, $storeProduct['pivot']['store_id']));
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'order placed successfully',
                'data' => [],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('add Order error' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_type' => 'required|numeric|in:3'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            return response()->json([
                'status' => true,
                'message' => 'orders',
                'data' =>$this->orderRepository->getRequestedOrdersByUser(UserId::UserId($request))
            ], AResponseStatusCode::SUCCESS);
          //  return new OrdersCollection($this->orderRepository->getOrdersByStatus($request));
        } catch (\Exception $e) {
            Log::error('error in salesOrders of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
    /**
     * Display the specified resource.
     * @param Request $request
     * @param int $id
     */
    public function show(Request $request, $id)
    {
        try {
            $order=$this->orderRepository->getOrderDetails($id,$this->lang,true);
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
}
