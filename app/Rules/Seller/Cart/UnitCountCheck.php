<?php

namespace App\Rules\Seller\Cart;

use Illuminate\Contracts\Validation\Rule;
use App\Models\PackingUnitProduct;

class UnitCountCheck implements Rule
{
    private $productId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($productId = null)
    {
        $this->productId = $productId;
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
        // Validate  if the quantity is available in the packing unit product
        $productId = $this->productId ?? request()['product_id'];

        $packingUnit = PackingUnitProduct::query()
            ->with('attributes')
            ->where('packing_unit_product.basic_unit_count', '>', 1)
            ->where('product_id', '=', $productId)
            ->first();

        if (!$packingUnit || !$productId ) {
            return false;
        }

        $qty = request()['quantity'];
        $basicUnitCount = $packingUnit->basic_unit_count;

        $quantityCheck = abs(($qty / $basicUnitCount) - round($qty / $basicUnitCount, 0)) < 0.0001;

        if ($quantityCheck == 1) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('messages.cart.invalid_quantity');
    }
}
