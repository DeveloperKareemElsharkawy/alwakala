<?php

namespace App\Http\Requests\Places\Countries;

use App\Http\Requests\Places\UpdateRequest;
use Illuminate\Http\Request;

class UpdateCountriesRequest extends UpdateRequest
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
     * @param $table
     * @param Request $request
     * @return array
     */
    public function rules(Request $request, $table = 'countries')
    {
        return array_merge(parent::rules($request, $table), [
            //extra rules including overrides
            'iso' => 'required|max:255|unique:' . $table . ',iso,' . $request->id,
            'country_code' => 'required|max:255|unique:' . $table . ',country_code,' . $request->id,
            'phone_code' => 'required|numeric|unique:' . $table . ',phone_code,' . $request->id,
        ]);
    }
}
