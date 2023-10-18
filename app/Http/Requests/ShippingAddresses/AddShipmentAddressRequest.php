<?php

namespace App\Http\Requests\ShippingAddresses;

use Illuminate\Foundation\Http\FormRequest;

class AddShipmentAddressRequest extends FormRequest
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
            'city_id' => 'required',
            'fees' => 'required',
            'store_id' => 'required',
        ];
    }
}
