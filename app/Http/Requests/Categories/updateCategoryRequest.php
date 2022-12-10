<?php

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class updateCategoryRequest extends FormRequest
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
            'id' => 'required|numeric|exists:categories,id',
            'name_ar' => 'required|max:255|unique:categories,name_ar,' . $request->id,
            'name_en' => 'required|max:255|unique:categories,name_en,' . $request->id,
            'description' => 'nullable',
//            'priority' => 'required',
            'activation' => 'required',
            'is_seller' => 'required',
            'is_consumer' => 'required',
        ];
    }
}
