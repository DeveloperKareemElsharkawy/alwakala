<?php

namespace App\Http\Requests\Materials;

use Illuminate\Foundation\Http\FormRequest;

class CreateMaterialRequest extends FormRequest
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
            'name_ar' => 'required|unique:materials,name_ar|max:255|min:3|regex:/[أ-ي]/u',
            'name_en' => 'required|unique:materials,name_en|max:255|min:3|regex:/[a-zA-Z]/u',
        ];
    }
}
