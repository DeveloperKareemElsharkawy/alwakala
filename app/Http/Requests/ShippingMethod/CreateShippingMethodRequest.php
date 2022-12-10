<?php

namespace App\Http\Requests\ShippingMethod;

use Illuminate\Foundation\Http\FormRequest;

class CreateShippingMethodRequest extends FormRequest
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
            'name_ar' => 'required|max:255|regex:/[أ-ي]/u|unique:shipping_methods',
            'name_en' => 'required|max:255||regex:/[a-zA-Z]/u|unique:shipping_methods',
            'activation' => 'required|boolean',
        ];
    }
}
