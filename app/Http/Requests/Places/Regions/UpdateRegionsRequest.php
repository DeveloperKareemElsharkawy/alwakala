<?php

namespace App\Http\Requests\Places\Regions;

use App\Http\Requests\Places\UpdateRequest;
use Illuminate\Http\Request;

class UpdateRegionsRequest extends UpdateRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request, $table = 'regions')
    {
        return array_merge(parent::rules($request, $table), [
            'country_id' => 'required|max:255|exists:countries,id',
        ]);
    }
}
