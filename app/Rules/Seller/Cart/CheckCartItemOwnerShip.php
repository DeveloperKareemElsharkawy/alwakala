<?php

namespace App\Rules\Seller\Cart;

use App\Lib\Helpers\Product\ProductHelper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PackingUnitProduct;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\Store;
use Illuminate\Contracts\Validation\Rule;

class CheckCartItemOwnerShip implements Rule
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
        $storeId = $this->storeId ?? request()['store_id'];

        $productStoreOwner = Store::query()->find($storeId);

        $store = Store::query()
            ->where('user_id', request()['user_id'])
            ->first();

        if (!$store || !$productStoreOwner){
            return false;
        }

        if ($store->id == $productStoreOwner->id) {
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
        return trans('messages.cart.you_own_this_product');
    }
}
