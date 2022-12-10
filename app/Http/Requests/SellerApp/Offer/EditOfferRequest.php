<?php

namespace App\Http\Requests\SellerApp\Offer;

use Illuminate\Foundation\Http\FormRequest;

class EditOfferRequest extends FormRequest
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
            'id' => 'required|numeric|exists:offers,id,user_id,' . $this->user_id,
            'name' => 'required|string|max:255',
            'from' => '',
            'to' => '',
            'activation'=>'',
            'bulk_price' => ['required' , 'numeric'],
            'retail_price' => ['required' , 'numeric'],
            'products' => 'required|array',
            'products.*.product_id' => 'required|numeric|exists:products,id,owner_id,' . $this->user_id,
        ];
    }
}
