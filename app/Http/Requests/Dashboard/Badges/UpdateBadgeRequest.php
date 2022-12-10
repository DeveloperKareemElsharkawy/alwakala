<?php

namespace App\Http\Requests\Dashboard\Badges;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateBadgeRequest extends FormRequest
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
            'id' => 'required|numeric|exists:badges,id',
            'name_ar' => 'nullable|max:255|regex:/[أ-ي]/u|unique:badges,name_ar,' . request()->id,
            'name_en' => 'nullable|max:255||regex:/[a-zA-Z]/u|unique:badges,name_en,' . request()->id,
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:512',
            'color_id' => 'nullable|exists:colors,id',
        ];
    }
}
