<?php

namespace App\Http\Requests\Consumer\Store;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStoreRequest extends FormRequest
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
            'review' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2024',
            'rate' => 'required|numeric|min:1|max:5',

        ];
    }
}
