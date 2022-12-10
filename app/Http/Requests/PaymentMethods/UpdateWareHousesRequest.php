<?php

namespace App\Http\Requests\PaymentMethods;

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
            'id' => 'required|numeric|exists:payment_methods,id',
            'name_ar' => 'nullable|max:255|regex:/[أ-ي]/u|unique:payment_methods,name_ar,' . request()->id,
            'name_en' => 'nullable|max:255||regex:/[a-zA-Z]/u|unique:payment_methods,name_en,' . request()->id,
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'activation' => 'nullable|boolean',
        ];
    }
}
