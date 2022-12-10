<?php

namespace App\Http\Requests\Places\Areas;

use App\Http\Requests\Places\CreateRequest;

class CreateAreasRequest extends CreateRequest
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
     * @param $table
     * @return array
     */
    public function rules($table = 'areas')
    {
        return array_merge(parent::rules($table), [
            'city_id' => 'required|max:255|exists:cities,id',
        ]);
    }
}
