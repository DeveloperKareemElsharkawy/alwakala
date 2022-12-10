<?php

namespace App\Http\Requests\PackingUnits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdatePackingUnitRequest extends FormRequest
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
            'name_ar' => 'required|max:255|unique:packing_units,name_ar,' . $request->id,
            'name_en' => 'required|max:255|unique:packing_units,name_en,' . $request->id,
            'name_cn' => 'required|max:255|unique:packing_units,name_cn,' . $request->id,
            'name_tr' => 'required|max:255|unique:packing_units,name_tr,' . $request->id,
        ];
    }
}
