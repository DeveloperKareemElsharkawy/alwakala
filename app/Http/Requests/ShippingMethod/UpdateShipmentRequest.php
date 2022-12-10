<?php

namespace App\Http\Requests\ShippingMethod;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
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
     * @param $table
     * @return array
     */
    public function rules(): array
    {

        return [
            'id' => 'required|numeric|exists:shipping_methods,id',
            'name_ar' => 'nullable|max:255|regex:/[أ-ي]/u|unique:shipping_methods,name_ar,' . request()->id ,
            'name_en' => 'nullable|max:255||regex:/[a-zA-Z]/u|unique:shipping_methods,name_en,' .  request()->id,
            'description_en' => 'nullable',
            'description_ar' => 'nullable',
            'activation' => 'nullable|boolean',
        ];
    }
}
