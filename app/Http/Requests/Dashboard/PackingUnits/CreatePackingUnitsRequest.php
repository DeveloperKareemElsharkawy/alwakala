<?php

namespace App\Http\Requests\Dashboard\PackingUnits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreatePackingUnitsRequest extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'name_ar' => 'required|max:255|unique:packing_units,name_ar',
            'name_en' => 'required|max:255|unique:packing_units,name_en',
        ];
    }
}
