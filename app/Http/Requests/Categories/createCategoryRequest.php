<?php

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;

class createCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            'name_ar' => 'required|max:255|iunique:categories,name_ar|regex:/[أ-ي]/u',
            'name_en' => 'required|max:255|iunique:categories,name_en',
            'description' => 'nullable',
            'is_consumer' => 'required',
            'is_seller' => 'required',
            'activation' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'category_id' => 'nullable|numeric|exists:categories,id',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name_ar.iunique' => 'Name Ar is used before',
            'name_en.iunique' => 'Name En is used before',
            'name_ar.regex' => 'Name Ar must be in Arabic',
        ];
    }
}
