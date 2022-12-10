<?php

namespace App\Http\Requests\PaymentMethods;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentMethodRequest extends FormRequest
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
            'name_ar' => 'required|max:255|regex:/[أ-ي]/u|unique:payment_methods,name_ar',
            'name_en' => 'required|max:255||regex:/[a-zA-Z]/u|unique:payment_methods,name_en',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'activation' => 'required|boolean',
        ];
    }

}
