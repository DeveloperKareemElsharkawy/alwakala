<?php

namespace App\Rules\Consumer\Cart;

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
    private $sizeId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($productId = null, $storeId = null, $colorId = null, $sizeId = null)
    {
        $this->colorId = $colorId;
        $this->productId = $productId;
        $this->storeId = $storeId;
        $this->sizeId = $sizeId;
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

        $colorId = $this->colorId ?? (int)request()['color_id'];
        $sizeId = $this->colorId ?? (int)request()['size_id'];
        $productId = $this->productId ?? (int)request()['product_id'];
        $storeId = $this->storeId ?? (int)request()['store_id'];
        $quantity = (int)$value;

        $productStore = ProductStore::query()->where([['store_id', $storeId], ['product_id', $productId]])->first();
        $productStoreStock = ProductStoreStock::query()->where([['product_store_id', $productStore?->id], ['color_id', $colorId], ['size_id', $sizeId]])->first();

        if (!$productStoreStock) {
            return false;
        }

        //   Start Check if Product is already in cart
        $cart = Cart::query()->firstOrCreate(['user_id' => request()->user_id]);

        $cartItem = CartItem::query()->where([
            ['cart_id', $cart['id']],
            ['user_id', request()['user_id']],
            ['product_id', $productId],
            ['color_id', $colorId],
            ['size_id', $sizeId],
            ['store_id',$storeId],
        ])->first();

        //    if Product in Cart Sub Quantity

        if ($cartItem) {
            $quantity += $cartItem->quantity;
        }

        if ($productStoreStock->available_stock < $quantity) {
            return false;
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
