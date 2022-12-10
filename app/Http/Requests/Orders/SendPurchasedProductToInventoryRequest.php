<?php

namespace App\Http\Requests\Orders;

use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class SendPurchasedProductToInventoryRequest extends FormRequest
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

        $rules = [
            'order_product_id' => 'required|exists:order_products,id',
        ];

        $orderProduct = OrderProduct::query()->find($this->order_product_id);

        if ($orderProduct) {
            $product = Product::query()->find($orderProduct->product_id);
            if ($product->policy_id == 2) {
                $rules['images'] = 'array';
                $rules['consumer_price'] = 'required|numeric|digits_between:1,5';
                $rules['consumer_old_price'] = 'nullable|numeric|digits_between:1,5';
            }
        }

        return $rules;

    }
}
