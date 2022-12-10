<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RateStoreRequest extends FormRequest
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
            'store_id' => 'required|numeric|exists:stores,id',
            'rate' => 'required|numeric|min:1|max:5',
            'review' => 'required|max:255',
        ];
    }
}
