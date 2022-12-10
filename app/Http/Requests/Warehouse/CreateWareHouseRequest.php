<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class CreateWareHouseRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'name_ar' => 'required|max:255|unique:warehouses,name_ar',
            'name_en' => 'required|max:255|unique:warehouses,name_en',
            'address_en' => 'required|max:500',
            'address_ar' => 'required|max:500',
            // 'activation' => 'required|boolean',
            'city_id' => '',
            'store_type_id' => 'required',
            'latitude' => '',
            'longitude' => '',
        ];
    }

}
