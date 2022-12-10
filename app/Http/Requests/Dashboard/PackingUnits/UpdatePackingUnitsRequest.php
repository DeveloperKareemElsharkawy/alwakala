<?php

namespace App\Http\Requests\Dashboard\PackingUnits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdatePackingUnitsRequest extends FormRequest
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
            'id' => 'required|numeric|exists:packing_units,id',
            'name_ar' => 'nullable|max:255|regex:/[أ-ي]/u|unique:packing_units,name_ar,' . request()->id,
            'name_en' => 'nullable|max:255||regex:/[a-zA-Z]/u|unique:packing_units,name_en,' . request()->id,
        ];
    }
}
