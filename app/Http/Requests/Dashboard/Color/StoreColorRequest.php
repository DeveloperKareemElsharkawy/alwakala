<?php

namespace App\Http\Requests\Dashboard\Color;

use Illuminate\Foundation\Http\FormRequest;

class StoreColorRequest extends FormRequest
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
            'name_ar' => ['required', 'max:255', 'unique:colors,name_ar', 'regex:/[أ-ي]/u'],
            'name_en' => ['required', 'max:255', 'unique:colors,name_en'],
            'hex' => ['required', 'unique:colors,hex', 'regex:/^(#(?:[0-9a-f]{2}){2,4}|#[0-9a-f]{3}|(?:rgba?|hsla?)\((?:\d+%?(?:deg|rad|grad|turn)?(?:,|\s)+){2,3}[\s\/]*[\d\.]+%?\))$/i'],
        ];
    }

    public function messages()
    {
        return [
            'name_ar.unique' => 'Name Ar is used before',
            'name_en.unique' => 'Name En is used before',
            'hex.unique' => 'Hex is used before',
        ];
    }
}
