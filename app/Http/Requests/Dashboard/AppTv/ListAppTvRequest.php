<?php

namespace App\Http\Requests\Dashboard\AppTv;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ListAppTvRequest extends FormRequest
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
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'expiry' => 'nullable|in:valid,expired',
        ];
    }
}
