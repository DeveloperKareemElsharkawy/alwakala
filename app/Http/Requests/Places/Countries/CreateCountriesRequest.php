<?php

namespace App\Http\Requests\Places\Countries;

use App\Http\Requests\Places\CreateRequest;

class CreateCountriesRequest extends CreateRequest
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
     * @param
     * @return array
     */
    public function rules($table = 'countries')
    {
        return array_merge(parent::rules($table), [
            //extra rules including overrides
           // 'iso' => 'required|max:255|unique:'.$table,
//            'country_code' => 'required|max:255|unique:'.$table,
//            'phone_code' => 'required|numeric|unique:'.$table,
        ]);
    }
}
