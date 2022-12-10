<?php

namespace App\Http\Requests\Measurements;

use App\Rules\Measurements\MeasurementStoreDuplicatedCheck;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeasurementsRequest extends FormRequest
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
            'size_id' => ['required','exists:sizes,id',new MeasurementStoreDuplicatedCheck],
            'category_id' => 'required|exists:categories,id',

            'length' => 'required_without:hem',
            'shoulder' => 'required_with:length',
            'chest' => 'required_with:length',
            'waist' => 'required_with:length',

            'hem' => 'required_without:length',
            'arm' => 'required_with:hem',
            'biceps' => 'required_with:hem',
            's_l' => 'required_with:hem',
        ];
    }
}
