<?php

namespace App\Http\Requests\Places\Zones;

use App\Http\Requests\Places\UpdateRequest;
use Illuminate\Http\Request;

class UpdateZonesRequest extends UpdateRequest
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
    public function rules(Request $request, $table = 'zones')
    {
        return array_merge(parent::rules($request, $table), [
            'area_id' => 'required|max:255|exists:zones,id',
        ]);
    }
}
