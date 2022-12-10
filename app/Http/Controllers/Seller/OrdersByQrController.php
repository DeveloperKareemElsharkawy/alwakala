<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Orders\AOrders;
use App\Enums\Product\AProductStatus;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Stock\ATransactionTypes;
use App\Enums\StoreTypes\StoreType;
use App\Events\Inventory\StockMovement;
use App\Events\Order\ApproveOrder;
use App\Events\Order\PlaceOrder;
use App\Events\Order\ReceiveOrder;
use App\Events\Order\RejectOrder;
use App\Events\Order\ShippingOrder;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\ApproveOrders;
use App\Http\Requests\Orders\ApproveOrRejectOrders;
use App\Http\Requests\Orders\CancelOrderProducts;
use App\Http\Requests\Orders\CancelOrders;
use App\Http\Requests\Orders\SendPurchasedProductToInventoryRequest;
use App\Http\Requests\Orders\RejectOrderProductRequest;
use App\Http\Requests\SellerApp\Order\AddOrderRequest;
use App\Http\Requests\SellerApp\Order\OrderGetRequest;
use App\Http\Requests\SellerApp\Order\OrdersGetRequest;
use App\Http\Requests\SellerApp\QRCode\AcceptOrderByBarcode;
use App\Http\Requests\SellerApp\QRCode\MakeOrderByQrRequest;
use App\Http\Resources\Dashboard\Orders\OrderProductResource;
use App\Http\Resources\Seller\Orders\OrderDetailsResource;
use App\Http\Resources\Seller\Orders\OrderDetailsStoreResource;
use App\Http\Resources\Seller\Orders\OrdersCollection;
use App\Http\Resources\Seller\Orders\OrdersDetailsResource;
use App\Http\Resources\Seller\Orders\OrdersResource;
use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Lib\Helpers\Orders\OrderHelper;
use App\Lib\Helpers\Pagination\PaginationHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Bundle;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\PackingUnit;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductShoppingCart;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\ShoppingCart;
use App\Models\Size;
use App\Models\Store;
use App\Repositories\InventoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShoppingCartRepository;
use App\Repositories\StoreRepository;
use App\Services\Orders\CreateOrderService;
use App\Services\Orders\OrdersService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Picqer\Barcode\BarcodeGeneratorPNG;

class OrdersByQrController extends BaseController
{
    public $shoppingCartRepo, $ordersService, $productRepo;
    private $lang;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * OrdersController constructor.
     * @param ShoppingCartRepository $shoppingCartRepo
     * @param ProductRepository $productRepository
     * @param OrdersService $ordersService
     * @param OrderRepository $orderRepository
     * @param OrdersService $ordersService
     * @param CartRepository $cartRepository
     * @param Request $request
     */
    public function __construct(ShoppingCartRepository $shoppingCartRepo,
                                ProductRepository      $productRepository,
                                OrdersService          $ordersService,
                                CartRepository         $cartRepository,
                                OrderRepository        $orderRepository,
                                Request                $request)
    {
        $this->shoppingCartRepo = $shoppingCartRepo;
        $this->productRepo = $productRepository;
        $this->ordersService = $ordersService;
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }


    public function createOrderByBarcode(MakeOrderByQrRequest $request, CreateOrderService $createOrderService)
    {
        try {
            $currentShoppingCarts = $this->cartRepository->getCartByUserId($request);

            if (count($currentShoppingCarts->items->where('store_id', $request['store_id'])) == 0) {
                return $this->error(['message' => trans('messages.order.no_cart_available')]);
            }

            DB::beginTransaction();
            $response = $createOrderService->createOrderByBarcode($currentShoppingCarts, $request);
            if (!$response['status']) {
                DB::rollBack();
                return $this->error(['message' => $response['message']]);
            }

            DB::commit();

            return $this->success([
                'message' => 'orders',
                'data' => new OrderDetailsResource($response['order']),
            ]);
            return $this->success(['message' => trans('messages.order.add')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
            Log::error('error in addOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function acceptOrderByBarcode(AcceptOrderByBarcode $request, CreateOrderService $createOrderService)
    {
        try {


            $order = Order::query()->where('number', $request['order_number'])->first();
            $order->status_id = AOrders::RECEIVED;
            $order->save();

            return $this->success(['message' => trans('messages.order.received'), 'data' => []]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
            Log::error('error in addOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
