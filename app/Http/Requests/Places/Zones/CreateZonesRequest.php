<?php

namespace App\Http\Requests\Places\Zones;

use App\Http\Requests\Places\CreateRequest;

class CreateZonesRequest extends CreateRequest
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
    public function rules($table = 'zones')
    {
        return array_merge(parent::rules($table), [
            'area_id' => 'required|max:255|exists:zones,id',
        ]);
    }
}
