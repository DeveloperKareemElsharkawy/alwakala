<?php

namespace App\Http\Requests\SellerApp;

use Illuminate\Foundation\Http\FormRequest;

class CreateBrachRequest extends FormRequest
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
            'name' => 'required|max:255|string',
            'address' => 'required|max:255|string',
//            'building_no' => 'required|numeric',
//            'landmark' => 'required|string',
//            'main_street' => 'required|string',
//            'side_street' => 'required|max:255|string',
            'is_main_branch' => 'required|min:0|max:1'
        ];
    }
}
