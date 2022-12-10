<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWareHousesRequest extends FormRequest
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
            'id' => 'required|numeric|exists:warehouses,id',
            'name_ar' => 'nullable|max:255|regex:/[أ-ي]/u|unique:warehouses,name_ar,' . request()->id,
            'name_en' => 'nullable|max:255||regex:/[a-zA-Z]/u|unique:warehouses,name_en,' . request()->id,
            'address_en' => 'nullable|max:500||regex:/[a-zA-Z]/u',
            'address_ar' => 'nullable|max:500|regex:/[أ-ي]/u',
            // 'activation' => 'nullable|boolean',
            'city_id' => '',
            'store_type_id' => 'required',
            'latitude' => '',
            'longitude' => '',
        ];
    }
}
