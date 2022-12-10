<?php

namespace App\Http\Requests\SystemSetup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSetupRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'=> 'required|numeric|exists:system_setups',
            'value'=> 'required|max:255',
        ];
    }
}
