<?php

namespace App\Services\Orders;

use App\Enums\Orders\AOrders;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Order\PlaceOrder;
use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ParentOrder;
use App\Models\ShoppingCart;
use App\Models\Store;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShoppingCartRepository;
use App\Repositories\StoreRepository;
use Carbon\Carbon;

class OrdersService
{
    private $orderRepository, $productRepository, $storeRepository, $shoppingCartRepository;

    public function __construct(OrderRepository        $orderRepository,
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

    public function insertOrderData($currentShoppingCarts): array
    {
        $productStocks = [];
        $storesUsersIds = [];
        $userId = $currentShoppingCarts[0]->user_id;
        $orderProducts = [];
        $parentOrder = new ParentOrder();
        $parentOrder->user_id = $userId;
        $parentOrder->order_price = 0;
        $parentOrder->save();
        foreach ($currentShoppingCarts as $ShoppingCart) {
            $store = $this->storeRepository->getStore($ShoppingCart->store_id);
            $storesUsersIds[] = $store->user_id;
            $order = new Order();
            $order->user_id = $ShoppingCart->user_id;
            $order->discount = 0;
            $order->status_id = AOrders::ISSUED;
            $order->address = $store->address;
            $order->payment_method_id = $ShoppingCart->payment_method_id;
            $order->shopping_cart_id = $ShoppingCart->id;
            $order->total_price = 0;
            $order->order_price = 0;
            $order->store_id = $ShoppingCart->store_id;
            $order->order_address_id = $ShoppingCart->address_id;
            $order->number = 'S-' . $ShoppingCart->store_id . '-' . $ShoppingCart->user_id . '-' . time();
            $order->parent_order_id = $parentOrder->id;
            $order->save();
            $orderTotalPrice = 0;
            $purchasedItemsCount = 0;
            foreach ($ShoppingCart->products as $storeProduct) {
                $orderProduct['packing_unit_id'] = $storeProduct['pivot']['packing_unit_id'];
                $orderProduct['order_id'] = $order->id;
                $orderProduct['product_id'] = $storeProduct['pivot']['product_id'];
                $orderProduct['purchased_item_count'] = $storeProduct['pivot']['purchased_item_count'];
                $orderProduct['size_id'] = $storeProduct['pivot']['size_id'];
                $orderProduct['item_price'] = $storeProduct['pivot']['item_price'];
                $orderProduct['color_id'] = $storeProduct['pivot']['color_id'];
                $orderProduct['total_price'] = $storeProduct['pivot']['total_price'];
                $orderProduct['basic_unit_count'] = $storeProduct['pivot']['basic_unit_count'];
                $orderProduct['store_id'] = $storeProduct['pivot']['store_id'];
                $productStocks [] = $orderProduct;
                unset($orderProduct['store_id']);
//                $result = $this->productRepository->checkQuantities($orderProduct, $store['id']);
//                if ($result <= 0) {
//                    $message = trans('messages.order.no_quantities_available');
//                    return ['status' => false, 'message' => $message];
//                }
                $orderProducts [] = $orderProduct;
                $orderTotalPrice += $storeProduct['pivot']['total_price'];
                $purchasedItemsCount += $orderProduct['purchased_item_count'] * $orderProduct['basic_unit_count'];
            }
//            $offer = OffersHelper::checkOffer($ShoppingCart->store_id, $purchasedItemsCount, $orderTotalPrice);
//            if ($offer) {
//                $order->discount = $offer['discount'];
//                $order->offer_id = $offer['id'];
//            }
            $order->total_price = $orderTotalPrice;
            $order->calculateTotalPrice();
            event(new PlaceOrder([$store->user_id], $order->id));
        }
        $this->orderRepository->insertOrderProducts($orderProducts);
        $this->productRepository->adoptQuantities($productStocks);
        $this->shoppingCartRepository->deleteOldShoppingCarts($userId);
        $parentOrder->calculateParentOrderPrice();
        return ['status' => true];
    }

}
