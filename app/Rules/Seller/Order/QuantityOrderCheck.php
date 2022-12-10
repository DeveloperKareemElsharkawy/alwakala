<?php

namespace App\Rules\Seller\Order;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PackingUnitProduct;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use Illuminate\Contracts\Validation\Rule;

class QuantityOrderCheck implements Rule
{


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        dd($value);
        dd($attribute);
        $colorId = $this->colorId ?? request()['color_id'];
        $productId = $this->productId ?? request()['product_id'];
        $storeId = $this->storeId ?? request()['store_id'];


        // Validate  if the quantity is available in the packing unit product
        $packingUnit = PackingUnitProduct::query()
            ->with('attributes')
            ->where('packing_unit_product.basic_unit_count', '>', 1)
            ->where('product_id', '=', $productId)
            ->first();

        $productStore = ProductStore::query()->where([['store_id', $storeId], ['product_id', $productId]])->first();

        if (!$packingUnit || !$productStore) {
            return false;
        }
        $cart = Cart::query()->firstOrCreate(['user_id' => request()['user_id']]);
        $cartItem = CartItem::query()->where([
            ['cart_id', $cart['id']],
            ['user_id', request()['user_id']],
            ['product_id', request()['product_id']],
            ['color_id', request()['color_id']],
            ['store_id', request()['store_id']],
        ])->first();
        $quantity = request()['quantity'];
        if ($cartItem) {
            $quantity += $cartItem->quantity;
        }
        foreach ($packingUnit['attributes'] as $attribute) {
            $productStoreStock = ProductStoreStock::query()->where([['product_store_id', $productStore['id']], ['size_id', $attribute['size_id']], ['color_id', $colorId]])->first();
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
