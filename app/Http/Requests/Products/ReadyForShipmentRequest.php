<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ReadyForShipmentRequest extends FormRequest
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
            'warehouse_id' => $this->shipping == 1 ? 'required' : '',
            'products.*.id' => $this->shipping == 1 ? 'required|numeric|exists:products,id,owner_id,' . $this->user_id : '',
            'products.*.amount' => $this->shipping == 1 ? 'required|numeric': '',
            'products.*.size_id' => $this->shipping == 1 ? 'required|numeric': '',
            'products.*.color_id' => $this->shipping == 1 ? 'required|numeric': '',
        ];
    }
}
