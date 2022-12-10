<?php

namespace App\Http\Requests\Consumer\Address;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
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
            'street_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'building_no' => 'nullable|numeric',
            'city_id' => 'nullable|numeric||exists:cities,id',
            'landmark' => 'nullable|string|max:255',

        ];
    }
}
