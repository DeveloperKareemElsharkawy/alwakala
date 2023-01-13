<?php

namespace App\Http\Requests\ConsumerApp\Product;

use Illuminate\Foundation\Http\FormRequest;

class GetProductByBarcodeRequest extends FormRequest
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
        return [
            'barcode' => 'required|exists:product_store,barcode_text',
         ];
    }
}
