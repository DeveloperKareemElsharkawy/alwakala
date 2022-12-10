<?php

namespace App\Http\Requests\Dashboard\CoverArea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ListStoreCoverAreaRequest extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'store_id' => 'required|numeric|exists:stores,id',
        ];
    }
}
