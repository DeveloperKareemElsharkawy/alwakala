<?php

namespace App\Http\Requests\SellerApp;

use Illuminate\Foundation\Http\FormRequest;

class CreateStoreRequest extends FormRequest
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
            'name' => 'required|string',
            'store_type_id' => 'required|numeric|exists:store_types,id',
            'category_id' => 'required|exists:categories,id',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'landing_number' => 'required|string',
            'mobile' => 'required|string|size:11',
            'city_id' => 'required|exists:cities,id',
            'is_store_has_delivery' => 'required|boolean',
            'is_main_branch' => 'required|boolean',
            'image' => 'required',
            'licence' => 'required',
            'logo' => 'required',
        ];
    }
}
