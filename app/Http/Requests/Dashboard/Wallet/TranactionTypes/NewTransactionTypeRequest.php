<?php

namespace App\Http\Requests\Dashboard\Wallet\TranactionTypes;

use Illuminate\Foundation\Http\FormRequest;

class NewTransactionTypeRequest extends FormRequest
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
            'name_ar' => 'required|max:255|min:3|regex:/[أ-ي]/u|unique:',
            'name_en' => 'required|max:255|min:3||regex:/[a-zA-Z]/u|unique:',
            'type' => 'required|in:1,2'
        ];
    }
}
