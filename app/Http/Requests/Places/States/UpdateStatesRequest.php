<?php

namespace App\Http\Requests\Places\States;

use App\Http\Requests\Places\UpdateRequest;
use Illuminate\Http\Request;

class UpdateStatesRequest extends UpdateRequest
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
    public function rules(Request $request, $table = 'states')
    {
        return array_merge(parent::rules($request, $table), [
            'region_id' => 'required|max:255|exists:regions,id',
        ]);
    }
}
