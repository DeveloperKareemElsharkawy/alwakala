<?php

namespace App\Http\Requests\Places\Regions;

use App\Http\Requests\Places\CreateRequest;

class CreateRegionsRequest extends CreateRequest
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
    public function rules($table = 'regions')
    {
        return array_merge(parent::rules($table), [
            'country_id' => 'required|max:255|exists:countries,id',
        ]);
    }
}
