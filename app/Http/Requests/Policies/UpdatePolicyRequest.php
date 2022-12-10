<?php

namespace App\Http\Requests\Policies;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePolicyRequest extends FormRequest
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
            'id' => 'required|numeric|exists:policies,id',
            'name_ar' => 'nullable|max:255|regex:/[أ-ي]/u|unique:policies,name_ar,' . request()->id ,
            'name_en' => 'nullable|max:255||regex:/[a-zA-Z]/u|unique:policies,name_en,' .  request()->id,
            'description_ar' => 'nullable|unique:policies,description_ar,' . request()->id ,
            'description_en' => 'nullable|unique:policies,description_en,' .  request()->id,
            'activation' => 'nullable|boolean',
        ];
    }
}
