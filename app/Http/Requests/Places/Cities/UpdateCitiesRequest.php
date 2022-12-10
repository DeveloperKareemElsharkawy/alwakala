<?php

namespace App\Http\Requests\Places\Cities;

use App\Http\Requests\Places\UpdateRequest;
use Illuminate\Http\Request;

class UpdateCitiesRequest extends UpdateRequest
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
    public function rules(Request $request, $table = 'cities')
    {
        return [
            'name_ar' => 'required|max:255|unique:cities,name_ar,'. $request->id,
            'name_en' => 'required|max:255|unique:cities,name_en,'. $request->id,
            'activation' => 'required',
            'state_id' => 'required|max:255|exists:states,id',

        ];

    }
}
