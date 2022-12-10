<?php

namespace App\Http\Requests\Places\Cities;

use App\Http\Requests\Places\CreateRequest;

class CreateCitiesRequest extends CreateRequest
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
    public function rules($table = 'cities')
    {
        return array_merge(parent::rules($table), [
            'state_id' => 'required|max:255|exists:states,id',
        ]);
    }
}
