<?php

namespace App\Http\Requests\SellerApp\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentsRequest extends FormRequest
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'licence' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'store_id' => 'required|numeric|exists:stores,id'
        ];

    }
}
