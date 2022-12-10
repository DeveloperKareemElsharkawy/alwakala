<?php

namespace App\Services\ShoppingCarts;

use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Models\ShoppingCart;
use App\Repositories\PackingUnitProductRepository;
use App\Repositories\ProductStoreRepository;
use App\Repositories\ShoppingCartRepository;
use Carbon\Carbon;

class ShoppingCartsService
{
    private $shoppingCartRepo, $packingUnitProductRepository, $productStoreRepository;

    public function __construct(ShoppingCartRepository       $shoppingCartRepo,
                                PackingUnitProductRepository $packingUnitProductRepository,
                                ProductStoreRepository       $productStoreRepository
    )
    {
        $this->shoppingCartRepo = $shoppingCartRepo;
        $this->packingUnitProductRepository = $packingUnitProductRepository;
        $this->productStoreRepository = $productStoreRepository;
    }

    public function insertShoppingCartData($request) :array{
        $this->shoppingCartRepo->deleteOldShoppingCarts($request->user_id);
        $shoppingCartProducts = [];
        $groupedProducts = [];
        foreach ($request->products as $product) {
            $groupedProducts[$product['store_id']][] = $product;
        }
        $shoppingCartIds = [];
        foreach ($groupedProducts as $storeId => $products) {
            $shoppingCart = new ShoppingCart();
            $shoppingCart->user_id = $request->user_id;
            $shoppingCart->store_id = $storeId;
            $shoppingCart->address_id = AddressHelper::getDefaultAddress($request->user_id);
            $shoppingCart->save();
            $totalCartPrice = 0;
            $totalPurchasedItemsCount = 0;
            foreach ($products as $product) {
                $productPackingUnit = $this->packingUnitProductRepository->getProductPackingUnit($product['packing_unit_id'], $product['product_id']);
                $productStore = $this->productStoreRepository->getProductStore($product['product_id'], $product['store_id']);
                $itemPrice = $productStore->net_price;
                $shoppingCartProduct = [
                    "shopping_cart_id" => $shoppingCart->id,
                    "purchased_item_count" => $product['purchased_item_count'],
                    "item_price" => $itemPrice,
                    "total_price" => $itemPrice * $product['purchased_item_count'] * $productPackingUnit->basic_unit_count,
                    "product_id" => $product['product_id'],
                    "packing_unit_id" => $product['packing_unit_id'],
                    "store_id" => $product['store_id'],
                    "basic_unit_count" => $productPackingUnit->basic_unit_count,
                    "size_id" => $product['size_id'] ?? null,
                    "color_id" => $product['color_id'] ?? null,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ];
                $totalPurchasedItemsCount += $product['purchased_item_count'] * $productPackingUnit->basic_unit_count;
                array_push($shoppingCartProducts, $shoppingCartProduct);
                $totalCartPrice += ($itemPrice * $product['purchased_item_count'] * $productPackingUnit->basic_unit_count);
            }
            $offer = OffersHelper::checkOffer($storeId, $totalPurchasedItemsCount, $totalCartPrice);
            if ($offer) {
                $shoppingCart->discount = $offer['discount'];
                $shoppingCart->offer_id = $offer['id'];
            }
            $shoppingCart->total_price = $totalCartPrice;
            $shoppingCart->calculateTotalPrice();
            $shoppingCartIds[] = $shoppingCart->id;
        }
        $this->shoppingCartRepo->insertShoppingCartProducts($shoppingCartProducts);
        return $shoppingCartIds;
    }

}
