<?php

namespace App\Http\Requests\SellerApp\Cart;

use App\Models\PackingUnitProduct;
use App\Models\ProductStore;
use App\Rules\Seller\Cart\CheckCartItemOwnerShip;
use App\Rules\Seller\Cart\QuantityCheck;
use App\Rules\Seller\Cart\UnitCountCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AddCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'store_id' => ['required', 'numeric', 'exists:stores,id', new CheckCartItemOwnerShip()],
            'color_id' => 'required|numeric|exists:colors,id',
            'quantity' => ['required', new QuantityCheck()],//, new UnitCountCheck()],
        ];
    }

    protected function prepareForValidation()
    {
        $packingUnit = PackingUnitProduct::query()
            // ->with('attributes')
            // ->where('packing_unit_product.basic_unit_count', '>', 1)
            ->where('product_id', '=', request()['product_id'])
            ->first();

        $this->merge([
            'packing_unit_id' => $packingUnit->packing_unit_id ?? null,
            'basic_unit_count' => $packingUnit->basic_unit_count ?? null,
            // 'product_store_id' => $productStore->id,
        ]);
    }

}
