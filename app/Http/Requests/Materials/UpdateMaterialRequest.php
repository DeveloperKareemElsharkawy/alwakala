<?php

namespace App\Http\Requests\Materials;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
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
            'id' => 'required|exists:materials|numeric',
            'name_ar' => ['required', 'unique:materials,name_ar,' . $this->id, 'max:255', 'min:3', 'regex:/[أ-ي]/u'],
            'name_en' => ['required', 'unique:materials,name_en,' . $this->id, 'max:255', 'min:3', 'regex:/[a-zA-Z]/u'],
        ];
    }
}
