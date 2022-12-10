<?php

namespace App\Http\Requests\Policies;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @param $table
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'nullable|exists:policies,id',
            'name' => 'nullable|string|max:255',
            'sort_by_name_ar' =>  'nullable |string|in:asc,desc',
            'sort_by_name_en' => 'nullable |string|in:asc,desc',
            'sort_by_id' => 'nullable |string|in:asc,desc',
        ];
    }


}
