<?php

namespace App\Http\Requests\Dashboard\Badges;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateBadgeRequest extends FormRequest
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
            'name_ar' => 'required|max:255|unique:badges,name_ar|regex:/[أ-ي]/u',
            'name_en' => 'required|max:255|unique:badges,name_en',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:512',
            'color_id' => 'required|exists:colors,id',
        ];
    }
}
