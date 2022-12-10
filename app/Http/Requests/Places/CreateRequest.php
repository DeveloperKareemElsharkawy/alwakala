<?php

namespace App\Http\Requests\Places;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
     * @return array
     */
    public function rules($table)
    {
        return [
            'name_ar' => 'required|max:255|regex:/[أ-ي]/u|unique:'.$table,
            'name_en' => 'required|max:255||regex:/[a-zA-Z]/u|unique:'.$table,
            'activation' => 'required',
        ];
    }
}
