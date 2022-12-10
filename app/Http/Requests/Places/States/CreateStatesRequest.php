<?php

namespace App\Http\Requests\Places\States;

use App\Http\Requests\Places\CreateRequest;

class CreateStatesRequest extends CreateRequest
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
    public function rules($table = 'states')
    {
        return array_merge(parent::rules($table), [
            'region_id' => 'required|max:255|exists:regions,id',
        ]);
    }
}
