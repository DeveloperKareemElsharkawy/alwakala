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
use App\Http\Requests\Orders\ReceiveOrders;
use App\Http\Requests\Orders\SendPurchasedProductToInventoryRequest;
use App\Http\Requests\Orders\RejectOrderProductRequest;
use App\Http\Requests\SellerApp\Order\AddOrderRequest;
use App\Http\Requests\SellerApp\Order\MakeOrderRequest;
use App\Http\Requests\SellerApp\Order\OrderGetRequest;
use App\Http\Requests\SellerApp\Order\OrdersGetRequest;
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
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
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
use App\Repositories\PackingUnitRepository;
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

class OrdersController extends BaseController
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
     * @var RateHelper
     */
    private $rateHelper;

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
                                Request                $request,
                                RateHelper             $rateHelper
    )
    {
        $this->shoppingCartRepo = $shoppingCartRepo;
        $this->productRepo = $productRepository;
        $this->ordersService = $ordersService;
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->rateHelper = $rateHelper;

    }

    /**
     * @param Request $request
     * @return JsonResponse
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
                'message' => trans('messages.order.add'),
                'data' => [],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
            Log::error('error in addOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param MakeOrderRequest $request
     * @param CreateOrderService $createOrderService
     * @return JsonResponse
     */
    public function addOrderV2(MakeOrderRequest $request, CreateOrderService $createOrderService)
    {
        try {
            $currentShoppingCarts = $this->cartRepository->getCartByUserId($request);

            if (!$currentShoppingCarts || count($currentShoppingCarts->items) == 0)
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
            return ServerError::handle($e);
            Log::error('error in addOrder of seller Order' . __LINE__ . $e);
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
            $order = Order::query()
                ->find($request->id);
            if ($order->status_id != AOrders::ISSUED) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.order.cannot_cancel'),
                    'data' => [],
                ], AResponseStatusCode::FORBIDDEN);
            }
            $order->status_id = AOrders::CANCELED;
            $order->save();
            $product = OrderProduct::query()->where('order_id', $order->id)->first();
            $product->status_id = AOrders::CANCELED;
            // $product->delivery_date = $data['delivery_date'];
            $product->save();
            $orderProducts = $order->products;
            foreach ($orderProducts as $orderProduct) {

                $orderProduct->store_id = $orderProduct['pivot']['store_id'];
                $orderProduct->product_id = $orderProduct['pivot']['product_id'];
                $orderProduct->color_id = $orderProduct['pivot']['color_id'];
                $orderProduct->size_id = $orderProduct['pivot']['size_id'];
                $orderProduct->packing_unit_id = $orderProduct['pivot']['packing_unit_id'];
                $orderProduct->basic_unit_count = $orderProduct['pivot']['basic_unit_count'];
                // TODO clear stock movement
//                event(new StockMovement($orderProduct['pivot']['purchased_item_count'], $orderProduct['pivot']['product_id'], ATransactionTypes::CANCEL_PURCHASE_ORDER, $order['store_id']));

            }
            $this->productRepo->adoptQuantities($orderProducts);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => trans('messages.order.canceled'),
                'data' => [],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            Log::error('error in cancelOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param CancelOrders $request
     * @return JsonResponse
     */
    public function cancelOrderProduct(CancelOrderProducts $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->order_products_ids as $orderProductId) {

                $orderProduct = OrderProduct::query()->with('order')->find($orderProductId);

                if ($orderProduct->order && $orderProduct->order->user_id != $request->user_id) {
                    return $this->error(['message' => trans('messages.order.order_product_false_ownership')]);
                }

                if ($orderProduct->status_id != AOrders::ISSUED) {
                    return $this->error(['message' => trans('messages.order.cannot_cancel')]);
                }

                $orderProduct->status_id = AOrders::CANCELED;
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

                $orderProducts = $order->products;
                foreach ($orderProducts as $orderProduct) {
                    $orderProduct->store_id = $orderProduct['pivot']['store_id'];
                    $orderProduct->product_id = $orderProduct['pivot']['product_id'];
                    $orderProduct->color_id = $orderProduct['pivot']['color_id'];
                    $orderProduct->size_id = $orderProduct['pivot']['size_id'];
                    $orderProduct->packing_unit_id = $orderProduct['pivot']['packing_unit_id'];
                    $orderProduct->basic_unit_count = $orderProduct['pivot']['basic_unit_count'];
                    // TODO clear stock movement
                    event(new StockMovement($orderProduct['pivot']['purchased_item_count'], $orderProduct['pivot']['product_id'], ATransactionTypes::CANCEL_PURCHASE_ORDER, $orderProduct['store_id']));
                }

                $this->productRepo->adoptQuantities($orderProducts);

            }

            DB::commit();

            return $this->success(['message' => trans('messages.order.canceled'), 'data' => []]);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            Log::error('error in cancelOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param CancelOrders $request
     * @return JsonResponse
     */
    public function rejectOrderProduct(RejectOrderProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $orderProduct = OrderProduct::query()->with('order')->find($request->order_product_id);

            $store = Store::query()->where('user_id', $request->user_id)->first();

            if ($orderProduct->store_id != $store->id) {
                return $this->error(['message' => trans('messages.order.order_product_false_ownership')]);
            }

            if ($orderProduct->status_id != AOrders::ISSUED) {
                return $this->error(['message' => trans('messages.order.cannot_cancel')]);
            }

            $orderProduct->status_id = AOrders::REJECT;
            $orderProduct->save();


            $order = Order::query()->find($orderProduct->order_id);
            $activeOrderProducts = $order->items->whereNotIn('status_id', [AOrders::REJECT, AOrders::CANCELED]);
            $total_price = $activeOrderProducts->sum('total_price');
            $order->total_price = $total_price;

            if ($activeOrderProducts->count() == 0) {
                $order->status_id = AOrders::REJECT;
                $order->save();

            }

            $orderProducts = $order->products;
            foreach ($orderProducts as $orderProduct) {
                $orderProduct->store_id = $orderProduct['pivot']['store_id'];
                $orderProduct->product_id = $orderProduct['pivot']['product_id'];
                $orderProduct->color_id = $orderProduct['pivot']['color_id'];
                $orderProduct->size_id = $orderProduct['pivot']['size_id'];
                $orderProduct->packing_unit_id = $orderProduct['pivot']['packing_unit_id'];
                $orderProduct->basic_unit_count = $orderProduct['pivot']['basic_unit_count'];
                // TODO clear stock movement
                event(new StockMovement($orderProduct['pivot']['purchased_item_count'], $orderProduct['pivot']['product_id'], ATransactionTypes::CANCEL_PURCHASE_ORDER, $store['id']));
            }

            $this->productRepo->adoptQuantities($orderProducts);

            DB::commit();

            return $this->success(['message' => trans('messages.order.product.rejected'), 'data' => []]);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            Log::error('error in cancelOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param SendPurchasedProductToInventoryRequest $request
     * @return JsonResponse
     */
    public function receiveOrder(ReceiveOrders $request)
    {
        try {

            DB::beginTransaction();

            $orderProducts = OrderProduct::query()->whereIn('id', $request->order_products_ids)->get();

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

    public function sendPurchasedProductToInventoryRequest(SendPurchasedProductToInventoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $userId = UserId::UserId($request);

            $storeId = StoreId::getStoreID($request);

            $ordersIds = Order::query()->where('user_id', $userId)->pluck('id')->toArray();

            $orderProduct = OrderProduct::query()->where('status_id', AOrders::RECEIVED)
                ->where('added_to_inventory', false)
                ->whereIn('order_id', $ordersIds)->find($request->order_product_id);

            $productStore = ProductStore::query()->where([['store_id', $orderProduct->store_id], ['product_id', $orderProduct->product_id]])->first();

            if (!$orderProduct) {
                return $this->error(['message' => trans('messages.order.no_products_to_approve')]);
            }

            $orderProductId = $orderProduct->id;
            $order = Order::query()->with(['products' => function ($query) use ($orderProductId) {
                $query->where('order_products.id', $orderProductId);
            }])->find($orderProduct->order_id);

            $sellerStore = Store::query()->where('user_id', $request->user_id)->first();

            $orderProducts = $order->products;

            $productNotExists = $this->productRepo->checkProductsNotExistInSellerStore($orderProducts, $sellerStore->id, $order->products);

            if (count($productNotExists)) {
                return $this->error(['message' => trans('messages.order.consumer_unit_publish_date'), 'data' => $productNotExists]);
            }

            foreach ($orderProducts as $product) {
                $product->store_id = $orderProduct['store_id'];
                $product->product_id = $product['pivot']['product_id'];
                $product->color_id = $product['pivot']['color_id'];
                $product->size_id = $product['pivot']['size_id'];
                $product->packing_unit_id = $product['pivot']['packing_unit_id'];
                $product->basic_unit_count = $product['pivot']['basic_unit_count'];
                $product->purchased_item_count = $product['pivot']['purchased_item_count'];
                $product->price = $productStore->price;
                $product->net_price = $productStore->net_price;

                if ($product->policy_id == 2) {
                    $product->consumer_price = $request->consumer_price;
                } else {
                    $product->consumer_price = $productStore->consumer_price;
                }

                $product->publish_at = '2022-10-08';

                $packingUnitProduct = PackingUnitRepository::packingUnitProduct($product['pivot']['product_id']);
                $productAttrs = ProductRepository::productAttrs($packingUnitProduct->id);

                $this->productRepo->increaseStockInStore($product, $sellerStore->id, $productAttrs, $storeId, true);
                $this->productRepo->decreaseStockInStore($product, $productAttrs, $packingUnitProduct->id);
//                event(new StockMovement($product['pivot']['purchased_item_count'], $product['pivot']['product_id'], ATransactionTypes::PURCHASE_ORDER, $sellerStore->id));
//                event(new StockMovement($product['pivot']['purchased_item_count'], $product['pivot']['product_id'], ATransactionTypes::SALE, $order['store_id']));
            }
            $this->productRepo->adoptQuantities($orderProducts);
            // TODO remove it when check on bundle if it exists
//            if ($sellerStore->store_type_id == StoreType::SUPPLIER) {
//                foreach ($request->products as $p) {
//                    Bundle::query()->firstOrCreate(
//                        ['store_id' => $sellerStore->id, 'product_id' => $p['id']],
//                        ['price' => $p['price'], 'quantity' => 100000]
//                    );
//                }
//
//            }

            $orderProduct->added_to_inventory = true;
            $orderProduct->save();

            DB::commit();
            event(new ReceiveOrder([$order->user_id], $request->id));

            return $this->success(['message' => trans('messages.order.received'), 'data' => []]);

        } catch
        (\Exception $e) {
            return $e;
            DB::rollBack();
            Log::error('error in receiveOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return OrdersCollection|JsonResponse
     */
    public function PurchaseOrders(Request $request)
    {
        try {
             $pageSize = $request->pageSize ? $request->pageSize : 10;
            $userId = $request->user_id;
            $query = Order::query()
                ->where('orders.user_id', $userId)
                ->join('order_products', 'orders.id', '=', 'order_products.order_id')
                ->join('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
                ->join('stores', function ($join) use ($userId) {
                    $join->on('orders.user_id', '=', 'stores.user_id')
                        ->where('stores.user_id', '=', $userId);
                })
                ->join('cities', 'stores.city_id', '=', 'cities.id')
                ->select(
                    'orders.id',
                    "cities.name_" . $this->lang . " as city_name",
                    "order_statuses.status_" . $this->lang . " as status",
                    "order_statuses.status_en as color_key",
                    'orders.total_price',
                    'orders.number',
                    'orders.status_id',
                    'orders.discount',
                    'orders.created_at',

                    DB::raw('count(order_products.product_id) as product_count'),
                    DB::raw('SUM(order_products.purchased_item_count * order_products.basic_unit_count)  as item_count')
                )->groupBy('orders.id', 'cities.name_ar', 'order_statuses.status_en', 'order_statuses.status_ar', 'cities.name_' . $this->lang);

            if ($request->filled('filter_by_today') && $request->filter_by_today == 1) {
                $query->whereDate('orders.created_at', '=', date('Y-m-d'));
            }
            if ($request->filled('filter_by_date')) {
                $query->whereDate('orders.created_at', Carbon::parse($request->filter_by_date)->format('Y-m-d'));
            }
            if ($request->filled('filter_by_status')) {
                $query->where('orders.status_id', $request->filter_by_status);
            }
            if ($request->filled('filter_by_delivery_date')) {
                $query->where('delivery_date', Carbon::parse($request->filter_by_delivery_date)->format('Y-m-d'));
            }
            if ($request->filled('filter_by_city_filter')) {
                $query->where('city_id', $request->filter_by_city_filter);
            }
            if ($request->filled('sort_by_total_price') && in_array($request->sort_by_total_price, ['asc', 'desc'])) {
                $query->OrderBy('total_price', $request->sort_by_total_price);
            }

            return response()->json([
                'status' => true,
                'message' => 'orders',
                'data' => OrdersResource::collection($query->latest()->paginate($pageSize)),
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in PurchaseOrders of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param OrderGetRequest $request
     * @return OrdersCollection|JsonResponse
     */
    public function getOrder(OrderGetRequest $request)
    {
        try {
            if ($request->order_type == 2) {
                $object = $this->orderRepository->getOrderByUser($request);

                if (!$object)
                    return $this->error(['message' => trans('messages.order.order_product_false_ownership')]);

                $object = new OrderDetailsStoreResource($object);
            }


            if ($request->order_type == 3) {
                $user_id = UserId::UserId($request);

                $object = $this->orderRepository->getRequestedOrderByUser($request, $user_id);

                if (!$object)
                    return $this->error(['message' => trans('messages.order.order_product_false_ownership')]);

                $object = new OrderDetailsResource($object);
            }

            return $this->success([
                'message' => 'orders',
                'data' => $object
            ]);


        } catch (\Exception $e) {
            Log::error('error in salesOrders of seller Order' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderOld(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:orders,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $order = Order::query()
                ->select([
                    'orders.id',
                    'orders.total_price',
                    'orders.delivery_date',
                    'orders.number',
                    'orders.status_id',
                    'orders.address',
                    'orders.payment_method_id',
                    'orders.store_id',
                    'orders.user_id as user_id'
                ])
                ->with([
                    'status',
                    'products:name  as product_name,description',
                    'store:name,logo,id'
                ])
                ->withCount('products')
                ->find($request->id);
            $sellerStore = Store::query()->where('id', $order->store_id)->first();
            if ($order->user_id != $request->user_id && !$sellerStore) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.order.not_found'),
                    'data' => [],
                ], AResponseStatusCode::BAD_REQUEST);
            }

            if (is_null($order)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.order.not_found'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $order->store_name = $order->store->name;
            $order->store_logo = config('filesystems.aws_base_url') . $order->store->logo;
            $order->status_name = $order->status['status_' . $this->lang];
//            unset($order->id);
            unset($order->status);
            $order->status = $order->status_name;
            unset($order->status_name);
            $order->address = AddressHelper::getFullAddress($order->user_id, $this->lang);
            $itemsCount = 0;
            foreach ($order->products as $product) {
                $packing_unit = PackingUnit::query()->find($product->pivot->packing_unit_id);
                $image = ProductImage::query()
                    ->select('image')
                    ->where('color_id', $product->pivot->color_id)
                    ->where('product_id', $product->pivot->product_id)
                    ->first()->image;

                if (is_null($product->pivot->size_id)) {
                    $productStore = ProductStore::query()
                        ->select('id')
                        ->where('store_id', $sellerStore->id)
                        ->where('product_id', $product->pivot->product_id)
                        ->first();
                    $sizeIds = ProductStoreStock::query()
                        ->where('product_store_id', $productStore->id)
                        ->select('size_id')->get()->pluck('size_id');
                    $size = Size::query()->select('size')->whereIn('id', $sizeIds)->get()->pluck('size')->toArray();
                    $size = implode('-', $size);
                } else {
                    $size = $product->pivot->size_id;
                }
                $product->packing_unit_name = $packing_unit->name_ . $this->lang;
                $product->packing_unit_id = $product->pivot->packing_unit_id;
                $product->product_id = $product->pivot->product_id;
                $product->purchased_item_count = $product->pivot->purchased_item_count * $product->pivot->basic_unit_count;
                $itemsCount += $product->purchased_item_count;
//                $product->store_name = $order->store->name;
                $product->store_id = $order->store->id;
                $product->item_price = $product->pivot->item_price;
                $product->image = config('filesystems.aws_base_url') . $image;
                $product->total_price = (float)$product->pivot->total_price;
                $product->basic_unit_count = $product->pivot->basic_unit_count;
                $product->color_id = $product->pivot->color_id;
                $product->size_id = $product->pivot->size_id;
                $product->size = !is_null($size) ? $size : null;
                unset($product->pivot);
            }
            unset($order->store);
            $order->item_count = $itemsCount;
            $order->stack_holder = OrderHelper::orderStackHolder($order->user_id, $request->user_id);
            return response()->json([
                'status' => true,
                'message' => trans('messages.order.received'),
                'data' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('error in getOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return OrdersCollection|JsonResponse
     */
    public function salesOrders(OrdersGetRequest $request)
    {
        try {
            $data = $this->orderRepository->getOrdersByUser($request);

            if ($request->order_type == 3) {
                $user_id = UserId::UserId($request);
                $data = $this->orderRepository->getRequestedOrdersByUser($request, $user_id);
            }

            return $this->success(['message' => trans('messages.general.listed'), 'data' => ($data)]);


        } catch (\Exception $e) {
            dd($e);
            Log::error('error in salesOrders of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param ApproveOrders $request
     * @return JsonResponse
     */
    public function approveOrder(ApproveOrders $request)
    {
        try {
            $store_id = StoreId::getStoreID($request);

            $order = Order::query()->whereHas('items', function ($q) use ($store_id) {
                $q->where('store_id', $store_id)->where('status_id', AOrders::ISSUED);
            })->find($request->id);

            if (!$order) {
                return $this->error(['message' => trans('messages.order.no_products_to_approve')]);
            }

            $order->status_id = AOrders::IN_PROGRESS;
            $order->save();

            OrderProduct::query()->where([['order_id', $order->id], ['store_id', $store_id], ['status_id', AOrders::ISSUED]])
                ->update(['status_id' => AOrders::IN_PROGRESS]);

//            event(new ApproveOrder([$order->user_id], $order->id));

            return $this->success(['message' => trans('messages.order.approved'), 'data' => []]);

        } catch (\Exception $e) {
            return $e;
            Log::error('error in approveOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function shippingOrder(ApproveOrRejectOrders $request)
    {
        try {

            $sellerStore = Store::query()->where('user_id', $request->user_id)->first();

            $order = Order::query()->with('items')->whereHas('items', function ($q) use ($sellerStore) {
                $q->where('store_id', $sellerStore->id)->where('status_id', AOrders::IN_PROGRESS);
            })->find($request->id);

            if (!$order) {
                return $this->error(['message' => trans('messages.order.is_issued')]);
            }
            $activeOrderProducts = OrderProduct::query()->where([
                ['order_id', $order->id],
                ['store_id', $sellerStore->id],
                ['status_id', AOrders::IN_PROGRESS]
            ]);

            if ($activeOrderProducts->count() == 0) {
                return $this->error(['message' => trans('messages.order.only_approve_reject_yours')]);
            }

            $activeOrderProducts->update(['status_id' => AOrders::SHIPPING]);

            $checkOrderProducts = OrderProduct::query()->where('order_id', $order->id)
                ->whereIn('status_id', [AOrders::IN_PROGRESS, AOrders::ISSUED])->count();

            if ($checkOrderProducts == 0) {
                $order->status_id = AOrders::SHIPPING;
                $order->save();
            }

            event(new ShippingOrder([$order->user_id], $order->id));

            return $this->success(['message' => trans('messages.order.shipping'), 'data' => []]);

        } catch (\Exception $e) {
            return $e;
            Log::error('error in approveOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function rejectOrder(ApproveOrRejectOrders $request)
    {
        try {
            $order = Order::query()->with('items')->find($request->id);
            $store = Store::query()->where('user_id', $request->user_id)->first();
            $orderProducts = $order->items->where('store_id', $store['id'])->whereIn('status_id', [AOrders::IN_PROGRESS, AOrders::ISSUED]);

            // if not Order Products To Be Rejected
            if (!$orderProducts) {
                return $this->error(['message' => trans('messages.order.no_products_to_reject'), 'data' => []]);
            }

            DB::beginTransaction();

            OrderProduct::query()->whereIn('id', $orderProducts->pluck('id')->toArray())->update([
                'status_id' => AOrders::REJECT
            ]);

            $checkOrderProducts = OrderProduct::query()->where('order_id', $order->id)
                ->whereNotIn('status_id', [AOrders::REJECT, AOrders::CANCELED])->count();

            // if All Products in order is rejected or Canceled Change Order Status  Rejected
            if ($checkOrderProducts == 0) {
                $order->status_id = AOrders::REJECT;
                $order->save();
            }

            // So We Don't Do AdoptQuantities For Order Products More than once
            $orderProducts = $order->products->whereIn('order_product_id', $orderProducts->pluck('id')->toArray());

            foreach ($orderProducts as $orderProduct) {
                $orderProduct->store_id = $orderProduct['pivot']['store_id'];
                $orderProduct->product_id = $orderProduct['pivot']['product_id'];
                $orderProduct->color_id = $orderProduct['pivot']['color_id'];
                $orderProduct->size_id = $orderProduct['pivot']['size_id'];
                $orderProduct->packing_unit_id = $orderProduct['pivot']['packing_unit_id'];
                $orderProduct->basic_unit_count = $orderProduct['pivot']['basic_unit_count'];
            }

            $this->productRepo->adoptQuantities($orderProducts);
//            event(new RejectOrder([$order->user_id], $order->id));
            DB::commit();

            return $this->success(['message' => trans('messages.order.rejected'), 'data' => []]);

        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            Log::error('error in rejectOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function orderStatuses()
    {
        try {
            $orderStatuses = OrderStatus::query()
                ->select('id', 'status_' . $this->lang . ' as status', 'status_en as color_key')
                ->get();

            return $this->success(['message' => trans('messages.order.statuses'), 'data' => $orderStatuses]);

        } catch (\Exception $e) {
            Log::error('error in orderStatuses of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function reOrder(Request $request, $order_id)
    {
        try {
            $data['id'] = $order_id;
            $validator = Validator::make($data, [
                'id' => 'required|numeric|exists:orders,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            DB::beginTransaction();
            $products = OrderProduct::query()->where('order_id', $order_id)->get();
            $oldOrder = Order::query()->where('id', $order_id)->first();
            $orderProducts = [];
            $totalPurchasedItemsCount = 0;
            $orderTotalPrice = 0;
            $productStocks = [];
            $order = new Order();
//                $order->delivery_date = Carbon::now()->addDays(1)->toDateString();
            $order->user_id = $request->user_id;
            $order->discount = $oldOrder->discount;
            $order->offer_id = $oldOrder->offer_id;
            $order->status_id = AOrders::ISSUED;
            $order->address = $oldOrder->address;
            $order->payment_method_id = $oldOrder->payment_method_id;
            $order->shopping_cart_id = $oldOrder->shopping_cart_id;
            $order->store_id = $oldOrder->store_id;
            $order->address_id = $oldOrder->address_id;
            $order->number = 'S-' . $oldOrder->store_id . '-' . $oldOrder->user_id . '-' . time();
            $order->total_price = 0;
            $order->order_price = 0;
            $order->save();
            foreach ($products as $product) {

                $basicUnitCount = PackingUnitProduct::query()
                    ->where('packing_unit_id', $product['packing_unit_id'])
                    ->where('product_id', $product['product_id'])
                    ->first();

                $itemPrice = ProductStore::query()
                    ->select('net_price')
                    ->where('product_id', $product['product_id'])
                    ->where('store_id', $oldOrder->store_id)
                    ->first()->net_price;

                $orderProduct = [
                    "order_id" => $order->id,
                    "purchased_item_count" => $product['purchased_item_count'],
                    "item_price" => $itemPrice,
                    "total_price" => $itemPrice * $product['purchased_item_count'] * $basicUnitCount->basic_unit_count,
                    "product_id" => $product['product_id'],
                    "packing_unit_id" => $product['packing_unit_id'],
                    "basic_unit_count" => $basicUnitCount->basic_unit_count,
                    "size_id" => $product['size_id'] ?? null,
                    "color_id" => $product['color_id'] ?? null
                ];
                $store = Store::query()->where('id', $oldOrder->store_id)->first();
                //   unset($orderProduct['store_id']);
                $result = $this->productRepo->checkQuantities($orderProduct, $store);
                if ($result <= 0) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('messages.order.no_quantities_available'),
                        'data' => [],
                    ], AResponseStatusCode::BAD_REQUEST);
                }
                $orderProduct['store_id'] = $oldOrder->store_id;
                $productStocks [] = $orderProduct;
                unset($orderProduct['store_id']);
                $totalPurchasedItemsCount += $product['purchased_item_count'];
                array_push($orderProducts, $orderProduct);
                $orderTotalPrice += ($itemPrice * $product['purchased_item_count'] * $basicUnitCount->basic_unit_count);
            }
            $orderProduct['store_id'] = $oldOrder->store_id;
            OrderProduct::query()->insert($orderProducts);
            $order->total_price = $orderTotalPrice - $oldOrder->discount;
            $order->order_price = $orderTotalPrice - $oldOrder->discount;
            $order->save();
            $this->productRepo->adoptQuantities($productStocks);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.order.add'),
                'data' => [],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('reOrder in seller orders error ' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function changeStatusOfOrdersProducts(Request $request)
    {
        try {
            $orderProduct = OrderProduct::query()->where('id', $request['id'])->first();

            if (!$orderProduct)
                return $this->notFound();

            $orderProduct->status_id = $request['status_id'];
            $orderProduct->save();

            return response()->json([
                'status' => true,
                'message' => 'status changed',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return $e;
            Log::error('error in change status of dashboard orders' . __LINE__ . $e);
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
            $validator = Validator::make($request->all(), [
                'order_type' => 'required|numeric|in:1,2,3,4'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $parent = false;
            if ($request->order_type == 3) {
                $parent = true;
            }
            $order = $this->orderRepository->getOrderDetails($id, $this->lang, $parent);
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
     * Display the specified resource.
     * @param Request $request
     * @param int $id
     */
    public function purchasedProducts(Request $request)
    {
        try {

            $userId = $request->user_id;
            $ordersIds = Order::query()->where('user_id', $userId)->pluck('id')->toArray();
            $ordersProducts = OrderProduct::query()->whereIn('order_id', $ordersIds)
                ->where('added_to_inventory', false)
                ->where('status_id', AOrders::RECEIVED)
                ->get();

            $productStores = [];
            $productStoresIds = [];

            foreach ($ordersProducts as $ordersProduct) {

                $productStore = ProductStore::query()
                    ->where([['product_id', $ordersProduct->product_id], ['store_id', $ordersProduct->store_id]])->first();

                if ($productStore) {
                    $productStoresIds[] = $productStore->id;
                    $productStores[] = $productStore;
                }
            }
            $ordersProductsCollection = collect($ordersProducts);

            $products = InventoryRepository::getpurchasedProductsQuery($request, $productStoresIds);

            foreach ($products as $product) {
                $ordersProduct = $ordersProductsCollection->where('product_id', $product->product_id)
                    ->where('store_id', $product->store_id)->first();

                $product->discount = $product->discount . '%';
                if ($product->productImage->image) {
                    $product->image = config('filesystems.aws_base_url') . $product->productImage->image;
                }
                $product->status = ProductHelper::productStatus($product->available_stock, $product->publish_at_date, $product->reviewed);
                $product->order_product_id = $ordersProduct->id;
                $product->purchasing_item_price = $ordersProduct->item_price;
                $product->purchasing_price = $ordersProduct->total_price;
                $product->basic_unit_count = $ordersProduct->basic_unit_count;
                $product->purchased_item_count = $ordersProduct->purchased_item_count;
                unset($product->productImage);
                $product->rate = $this->rateHelper->getAverageRate($product->product_id, Product::class);
            }


            return $this->respondWithPagination($products);
        } catch (\Exception $e) {
            return $e;
            Log::error('error in show of dashboard orders' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
