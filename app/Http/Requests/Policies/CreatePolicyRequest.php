<?php

namespace App\Http\Requests\Policies;

use Illuminate\Foundation\Http\FormRequest;

class CreatePolicyRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name_ar' => 'required|max:255|regex:/[أ-ي]/u|unique:policies',
            'name_en' => 'required|max:255||regex:/[a-zA-Z]/u|unique:policies',
            'activation' => 'required|boolean',
        ];
    }
}
