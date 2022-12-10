<?php

namespace App\Http\Requests\Products;

use App\Models\BarcodeProduct;
use App\Models\Product;
use App\Models\ProductStore;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductByBarcodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'barcode' => 'required|exists:product_store,barcode_text',
            'free_shipping' => 'required|in:0,1',
            'discount' => 'required|numeric|min:0|max:100',
            'price' => 'required|numeric|min:1|max:99999999',

        ];

        $productStore = $this->barcode ? ProductStore::query()->where('barcode_text', $this->barcode)->first() : null;

        if ($productStore) {
            $product = Product::query()->find($productStore['product_id']);

            if ($product && !$product->consumer_price) {
                $rules['consumer_price'] = ['required', 'numeric', 'digits_between:1,5'];
            }
        }

        return $rules;
    }
}
