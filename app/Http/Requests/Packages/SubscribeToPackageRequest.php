<?php

namespace App\Http\Requests\Packages;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeToPackageRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'store_id' => 'nullable|numeric|exists:stores,id',
            'status' => 'nullable|numeric|in:0,1,2',
            'package_id' => 'required|numeric|exists:packages,id',
        ];

    }
}
