<?php

namespace App\Http\Requests\Dashboard\System;

use Illuminate\Foundation\Http\FormRequest;

class NewSystemSetupRequest extends FormRequest
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
        if (request()->isMethod('post')) {
            return [
                'title' => 'required|max:255|min:4|unique:system_setups,key',
                'value' => 'required|max:255|min:4',
            ];
        } elseif (request()->isMethod('put')) {
            return [
                'value' => 'required|max:255|min:4',
                'id' => 'required|numeric',
            ];
        }

    }

}
