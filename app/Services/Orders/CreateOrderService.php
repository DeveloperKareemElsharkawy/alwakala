<?php

namespace App\Services\Orders;

use App\Enums\Orders\AOrders;
use App\Enums\PaymentMethods\APaymentMethods;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Order\PlaceOrder;
use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\ProductOrderUnitDetails as ModelsProductOrderUnitDetails;
use App\Models\ProductStore;
use App\Models\ShoppingCart;
use App\Models\Store;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShoppingCartRepository;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Picqer\Barcode\BarcodeGeneratorPNG;
use ProductOrderUnitDetails;

class CreateOrderService
{
    private $orderRepository, $productRepository, $storeRepository, $shoppingCartRepository;

    public function __construct(
        OrderRepository        $orderRepository,
        ProductRepository      $productRepository,
        StoreRepository        $storeRepository,
        ShoppingCartRepository $shoppingCartRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->storeRepository = $storeRepository;
        $this->shoppingCartRepository = $shoppingCartRepository;
    }

    public function createOrder($currentShoppingCarts, $request)
    {
        $order_address_id = null;
        if ($currentShoppingCarts->address_id) {
            $address = $currentShoppingCarts->address->toArray();
            unset($address['id']);
            $order_address = OrderAddress::create($address);
            $order_address_id = $order_address->id;
        }
        $userId = $request['user_id'];
        $order = new Order();
        $order->user_id = $userId;
        $order->discount = 0;
        $order->payment_method_id = $currentShoppingCarts->payment_method_id;
        $order->total_price = 0;
        $order->order_address_id = $order_address_id;
        $order->number = 'S-' . $userId . '-' . time();
        $order->save();
        $order->status()->insert(['order_id' => $order->id, 'order_status_id' => 1]);

        foreach ($currentShoppingCarts->items as $ShoppingCart) {
            $product_store_stock = $ShoppingCart->product_store->product_store_stock($ShoppingCart->color_id)->first();
            if ($ShoppingCart->quantity > $product_store_stock->available_stock) {
                $message = trans('messages.order.no_quantities_available');
                return ['status' => false, 'message' => $message];
            }
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $ShoppingCart->product_id;
            $orderProduct->color_id = $ShoppingCart->color_id;
            $orderProduct->store_id = $ShoppingCart->store_id;
            $orderProduct->quantity = $ShoppingCart->quantity;
            $orderProduct->basic_unit_count = $ShoppingCart->product->packingUnitProduct->basic_unit_count;
            $orderProduct->purchased_item_count = $orderProduct->quantity * $orderProduct->basic_unit_count;
            $orderProduct->item_price = $ShoppingCart->product_store->net_price;
            $orderProduct->total_price = $ShoppingCart->product_store->net_price * $ShoppingCart->quantity * $orderProduct->basic_unit_count;
            $orderProduct->save();
            foreach ($ShoppingCart->product->packingUnitProduct->attributes as $attribute) {
                $product_order_unit_details = new ModelsProductOrderUnitDetails();
                $product_order_unit_details->order_product_id = $orderProduct->id;
                $product_order_unit_details->size_id = $attribute->size_id;
                $product_order_unit_details->quantity = $attribute->quantity;
                $product_order_unit_details->save();
            }
            $product_store_stock->available_stock = $product_store_stock->available_stock - $ShoppingCart->quantity;
            $product_store_stock->save();
        }
        $order->calculateTotalPrice(true);
        CartItem::query()->where('user_id', $userId)->delete();
        Cart::query()->where('user_id', $userId)->delete();
        return ['status' => true];
    }

    public function createOrderByBarcode($currentShoppingCarts, $request)
    {
        $order_address_id = null;
        $userId = $request['user_id'];
        $order = new Order();
        $order->user_id = $userId;
        $order->discount = 0;
        $order->status_id = AOrders::WAITING_FOR_BARCODE_APPROVAL;
        $order->payment_method_id = APaymentMethods::COD;
        $order->total_price = 0;
        $order->status_id = AOrders::WAITING_FOR_BARCODE_APPROVAL;
        $order->order_address_id = $order_address_id;
        $order->number = 'S-' . $userId . '-' . time();
        $order->save();
        $order->status()->insert(['order_id' => $order->id, 'order_status_id' => 1]);

        foreach ($currentShoppingCarts->items->where('store_id', $request['store_id']) as $ShoppingCart) {
            $product_store_stock = $ShoppingCart->product_store->product_store_stock($ShoppingCart->color_id)->first();
            if ($ShoppingCart->quantity > $product_store_stock->available_stock) {
                $message = trans('messages.order.no_quantities_available');
                return ['status' => false, 'message' => $message];
            }
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $ShoppingCart->product_id;
            $orderProduct->color_id = $ShoppingCart->color_id;
            $orderProduct->store_id = $ShoppingCart->store_id;
            $orderProduct->quantity = $ShoppingCart->quantity;
            $orderProduct->basic_unit_count = $ShoppingCart->product->packingUnitProduct->basic_unit_count;
            $orderProduct->purchased_item_count = $orderProduct->quantity * $orderProduct->basic_unit_count;
            $orderProduct->item_price = $ShoppingCart->product_store->net_price;
            $orderProduct->total_price = $ShoppingCart->product_store->net_price * $ShoppingCart->quantity * $orderProduct->basic_unit_count;
            $orderProduct->save();
            foreach ($ShoppingCart->product->packingUnitProduct->attributes as $attribute) {
                $product_order_unit_details = new ModelsProductOrderUnitDetails();
                $product_order_unit_details->order_product_id = $orderProduct->id;
                $product_order_unit_details->size_id = $attribute->size_id;
                $product_order_unit_details->quantity = $attribute->quantity;
                $product_order_unit_details->save();
            }
            $product_store_stock->available_stock = $product_store_stock->available_stock - $ShoppingCart->quantity;
            $product_store_stock->save();
        }
        $order->calculateTotalPrice(true);

        $generator = new BarcodeGeneratorPNG();
        $order->barcode = UploadImage::uploadSVGToStorage($generator->getBarcode($order->number, $generator::TYPE_CODE_128, 3, 50, [0, 0, 0])); // upload qrcode image to s3 storage
        $order->save();
        CartItem::query()->where([['user_id', $userId], ['store_id', $request['store_id']]])->delete();
        return ['status' => true, 'order' => $order];
    }

    public function createOrderByQr($currentShoppingCarts, $request)
    {
        $userId = $request['user_id'];
        $order = new Order();
        $order->user_id = $userId;
        $order->discount = 0;
        $order->status_id = AOrders::WAITING_FOR_BARCODE_APPROVAL;
        $order->payment_method_id = APaymentMethods::COD;
        $order->total_price = 0;
        $order->number = 'S-' . $userId . '-' . time();
        $order->save();
        $order->status()->insert(['order_id' => $order->id, 'order_status_id' => 1]);

        foreach ($currentShoppingCarts as $ShoppingCart) {

            $product = Product::query()->find($ShoppingCart['product_id']);

            $productStore = ProductStore::query()->where([
                'product_id' => $ShoppingCart['product_id'],
                'store_id' => $ShoppingCart['store_id'],
            ])->first();

            $product_store_stock = $productStore->product_store_stock($ShoppingCart['color_id'])->first();

            if ($ShoppingCart['quantity'] > $product_store_stock->available_stock) {
                $message = trans('messages.order.no_quantities_available');
                return ['status' => false, 'message' => $message];
            }

            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $ShoppingCart['product_id'];
            $orderProduct->color_id = $ShoppingCart['color_id'];
            $orderProduct->store_id = $ShoppingCart['store_id'];
            $orderProduct->quantity = $ShoppingCart['quantity'];
            $orderProduct->basic_unit_count = $product->packingUnitProduct->basic_unit_count;
            $orderProduct->purchased_item_count = $orderProduct['quantity'] * $product->packingUnitProduct->basic_unit_count;
            $orderProduct->item_price = $productStore->net_price;
            $orderProduct->total_price = $productStore->net_price * $ShoppingCart['quantity'] * $product->packingUnitProduct->basic_unit_count;
            $orderProduct->save();

            foreach ($product->packingUnitProduct->attributes as $attribute) {
                $product_order_unit_details = new ModelsProductOrderUnitDetails();
                $product_order_unit_details->order_product_id = $orderProduct->id;
                $product_order_unit_details->size_id = $attribute->size_id;
                $product_order_unit_details->quantity = $attribute->quantity;
                $product_order_unit_details->save();
            }

            $product_store_stock->available_stock = $product_store_stock->available_stock - $ShoppingCart['quantity'];
            $product_store_stock->save();

        }
        $order->calculateTotalPrice(true);

        return ['status' => true];
    }

    public function createOrderOld($currentShoppingCarts, $request)
    {
        $productStocks = [];
        $storesUsersIds = [];

        $userId = $request['user_id'];
        $cart = $currentShoppingCarts['cart'];

        $orderProducts = []; // Group Orders By Parent Order
        $parentOrder = new ParentOrder();
        $parentOrder->user_id = $userId;
        $parentOrder->order_price = 0;
        $parentOrder->save();

        foreach ($currentShoppingCarts['stores'] as $ShoppingCart) {
            $store = $this->storeRepository->getStore($ShoppingCart->id);
            $storesUsersIds[] = $store->user_id;
            $order = new Order();
            $order->user_id = $userId;
            $order->discount = 0;
            $order->status_id = AOrders::ISSUED;
            $order->address = $store->address;
            $order->payment_method_id = $cart->payment_method_id;

            $order->total_price = 0;
            $order->order_price = 0;
            $order->store_id = $store->id;
            $order->address_id = $ShoppingCart->address_id;
            $order->number = 'S-' . $ShoppingCart->store_id . '-' . $ShoppingCart->user_id . '-' . time();
            $order->parent_order_id = $parentOrder->id;
            $order->save();

            $orderTotalPrice = 0;
            $purchasedItemsCount = 0;
            foreach ($ShoppingCart['items'] as $cartStoreProduct) {
                $orderProduct['packing_unit_id'] = $cartStoreProduct['packing_unit_id'];
                $orderProduct['order_id'] = $order->id;
                $orderProduct['product_id'] = $cartStoreProduct['product_id'];
                $orderProduct['purchased_item_count'] = $cartStoreProduct['quantity'];
                $orderProduct['item_price'] = $cartStoreProduct['net_price'];
                $orderProduct['color_id'] = $cartStoreProduct['color_id'];
                $orderProduct['total_price'] = $cartStoreProduct['sub_total'];
                $orderProduct['basic_unit_count'] = $cartStoreProduct['basic_unit_count'];
                $orderProduct['store_id'] = $cartStoreProduct['store_id'];
                $productStocks[] = $orderProduct;
                unset($orderProduct['store_id']);
                $result = $this->productRepository->checkQuantities($orderProduct, $store);
                if ($result <= 0) {
                    $message = trans('messages.order.no_quantities_available');
                    return ['status' => false, 'message' => $message];
                }
                $orderProducts[] = $orderProduct;
                $orderTotalPrice += $cartStoreProduct['sub_total'];
                $purchasedItemsCount += $orderProduct['purchased_item_count'] * $orderProduct['basic_unit_count'];
            }
            $offer = OffersHelper::checkOffer($store->id, $purchasedItemsCount, $orderTotalPrice);
            if ($offer) {
                $order->discount = $offer['discount'];
                $order->offer_id = $offer['id'];
            }
            $order->total_price = $orderTotalPrice;
            $order->calculateTotalPrice();
            //            event(new PlaceOrder([$store->user_id], $order->id));
        }

        $this->orderRepository->insertOrderProducts($orderProducts);
        $this->productRepository->adoptQuantities($productStocks);
        $this->shoppingCartRepository->deleteOldShoppingCarts($userId);
        $parentOrder->calculateParentOrderPrice();

        CartItem::query()->where('user_id', $userId)->delete();
        Cart::query()->where('user_id', $userId)->delete();

        return ['status' => true];
    }
}
