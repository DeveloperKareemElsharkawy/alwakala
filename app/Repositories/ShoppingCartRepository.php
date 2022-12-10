<?php


namespace App\Repositories;

use App\Lib\Helpers\Address\AddressHelper;
use App\Models\Bundle;
use App\Models\PackingUnitProductAttribute;
use App\Models\ProductShoppingCart;
use App\Models\ShoppingCart;
use App\Models\Store;

class ShoppingCartRepository
{
    protected $model;

    public function __construct(ShoppingCart $model)
    {
        $this->model = $model;
    }

    public function checkBundle(int $product_id, int $store_id, int $purchased_item_count)
    {

        $bundles = Bundle::query()
            ->where('product_id', $product_id)
            ->where('store_id', $store_id)
            ->where('quantity', '>=', $purchased_item_count)
            ->orderBy('quantity', 'asc')
            ->first();
        if (is_null($bundles)) {
            $bundles = Bundle::query()
                ->where('product_id', $product_id)
                ->where('store_id', $store_id)
                ->orderBy('quantity', 'desc')
                ->first();
        }

        return $bundles->price;
    }

    public function deleteProductFromCart($allDate, $currentShoppingCartId)
    {
        ProductShoppingCart::query()
            ->where('packing_unit_product_store_attributes_id', $allDate['packing_unit_product_store_attributes_id'])
            ->where('shopping_cart_id', $currentShoppingCartId)
            ->delete();
        ShoppingCart::query()->find($currentShoppingCartId)->calculateTotalPrice();
    }


    public function getCurrentShoppingCarts($userId)
    {
        return ShoppingCart::query()
            ->where('user_id', $userId)
            ->get();
    }
    public function deleteOldShoppingCarts($userId)
    {
        ShoppingCart::query()->where('user_id', $userId)->delete();
    }
    public function insertShoppingCartProducts($shoppingCartProducts)
    {
        ProductShoppingCart::query()->insert($shoppingCartProducts);
    }

    public function getCurrentShoppingCartForApp($userId, $lang)
    {

        $shoppingCart = ShoppingCart::query()
            ->where('user_id', $userId)
            ->with([
                'products:products.id,name,description',
//                'address:id,user_id,address,mobile',
                'payment_method:name_en,id'
            ])
            ->first();
        if (is_null($shoppingCart)) {
            return [];
        }
        $products = $shoppingCart->products;

        $arrayOfStoresIds = [];
        foreach ($products as $product) {
            if (!in_array($product->pivot->store_id, $arrayOfStoresIds)) {
                $arrayOfStoresIds[] = $product->pivot->store_id;
            }
            foreach ($product->images as $image) {
                $image->image = config('filesystems.aws_base_url') . $image->image;
            }
        }
        $stores = Store::query()
            ->whereIn('id', $arrayOfStoresIds)
            ->select('id', 'name', 'logo')
            ->get();

        $totalCartItems = 0;
        foreach ($stores as $store) {
            $store->logo = config('filesystems.aws_base_url') . $store->logo;
            $storeProducts = [];
            $items = 0;
            foreach ($products as $product) {
                if ($product->pivot->store_id == $store->id) {
                    $items = $product->pivot->purchased_item_count * $product->pivot->basic_unit_count;
                    $product->store_id = $store->id;
                    $product->purchased_item_count = $product->pivot->purchased_item_count;
                    $product->packing_unit_id = $product->pivot->packing_unit_id;
                    $product->item_price = $product->pivot->item_price;
                    $product->total_price = $product->pivot->total_price;
                    array_push($storeProducts, $product);
//                    unset($product->pivot);
                }
            }
            $store->products_count = count($storeProducts);
            $store->item_count = $items;
            $store->products = $storeProducts;
            $totalCartItems += $items;
        }

        return [
            'id' => $shoppingCart->id,
            'total_price' => $shoppingCart->total_price,
            'delivery_date' => $shoppingCart->date,
            'address' => AddressHelper::getFullAddress($userId, $lang),
            'total_items' => $totalCartItems,
            'payment_method' => $shoppingCart->payment_method,
            'stores' => $stores
        ];

    }

    public function getSellerStore($user_id)
    {
        return Store::query()->where('user_id', $user_id)->first();
    }
}
