<?php

namespace App\Http\Requests\SellerApp;

use Illuminate\Foundation\Http\FormRequest;

class AddAddressRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:home,work',
            'city_id' => 'required|numeric|exists:cities,id',
            'address' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'mobile' => 'required|mobile_number|size:11'
        ];
    }
}
