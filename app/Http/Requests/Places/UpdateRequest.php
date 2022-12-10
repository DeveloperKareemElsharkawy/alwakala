<?php

namespace App\Http\Requests\Places;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateRequest extends FormRequest
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
     * @param $table
     * @return array
     */
    public function rules(Request $request, $table)
    {
        return [
            'name_ar' => 'required|max:255|unique:'.$table.',name_ar,'. $request->id,
            'name_en' => 'required|max:255|unique:'.$table.',name_en,'. $request->id,
            'activation' => 'required',
        ];
    }
}
