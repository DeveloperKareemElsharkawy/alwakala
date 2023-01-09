<?php

namespace App\Rules\Seller\Cart;

use App\Enums\StoreTypes\StoreType;
use App\Lib\Helpers\Product\ProductHelper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PackingUnitProduct;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\Store;
use Illuminate\Contracts\Validation\Rule;

class QuantityCheck implements Rule
{
    private $colorId;
    /**
     * @var mixed|null
     */
    private $productId;
    /**
     * @var mixed|null
     */
    private $storeId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($productId = null, $storeId = null, $colorId = null)
    {
        $this->colorId = $colorId;
        $this->productId = $productId;
        $this->storeId = $storeId;
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $colorId = $this->colorId ?? request()['color_id'];
        $productId = $this->productId ?? request()['product_id'];
        $storeId = $this->storeId ?? request()['store_id'];
        $productStore = ProductStore::query()->where([['store_id', $storeId], ['product_id', $productId]])->first();
        $quantity = (int)request()['quantity'];
        $store = Store::query()->find($storeId);
        $basicUnitCount = ProductHelper::getProductBasicUnitCount($productId);

        //  Check if Seller Type is Supplier

        if ($store && $store->store_type_id == StoreType::SUPPLIER) {
            $quantity = $quantity * $basicUnitCount; // multiply Quantity by Basic Unit Count if
        }

        //   Start Check if Product is already in cart

        $cart = Cart::query()->firstOrCreate(['user_id' => request()['user_id']]);
        $cartItem = CartItem::query()->where([['cart_id', $cart['id']], ['user_id', request()['user_id']], ['product_id', request()['product_id']], ['color_id', request()['color_id']], ['store_id', request()['store_id']],])->first();

        //    if Product in Cart Sub Quantity

        if ($cartItem) {
            $quantity += $cartItem->quantity;
        }

        if (!$store || !$productStore)
            return false;

        $productStoreStocks = ProductStoreStock::query()->where([['product_store_id', $productStore->id], ['color_id', $colorId]])->get();


        if (!count($productStoreStocks))
            return false;

        foreach ($productStoreStocks as $productStoreStock) {
            if (!$productStoreStock || $productStoreStock->available_stock < $quantity) {
                return false;
            }
        }

        return true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public
    function message(): string
    {
        return trans('messages.cart.product_color_stock_empty');
    }
}
