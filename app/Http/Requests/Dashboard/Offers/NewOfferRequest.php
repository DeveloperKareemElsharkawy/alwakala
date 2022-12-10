<?php

namespace App\Http\Requests\Dashboard\Offers;

use http\Env\Request;
use Illuminate\Foundation\Http\FormRequest;

class NewOfferRequest extends FormRequest
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
        info(\request()->all());
        return [
            'name_ar' => 'required|max:255|min:4',
            'name_en' => 'required|max:255|min:4',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'description' => 'nullable|min:8|max:400',
            'activation' => 'required',
            'presenter_id' => 'required|numeric',
            'type_id' => 'required|numeric|in:1,2,3',
            'discount_value' => 'required|numeric|min:1',
            'discount_type' => 'required|in:1,2,3',
            'total_price' => 'nullable|min:0.01',
            'total_purchased_items' => 'nullable|integer|min:1',
            'from' => 'required',
            'to' => 'required',
            'max_usage_count' => 'required|integer|min:1'
        ];
    }
}
