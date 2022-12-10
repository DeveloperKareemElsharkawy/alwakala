<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array
     */
    public function rules(): array
    {
        return [
            // 'id' => 'nullable|exists:warehouses,id',
            // 'name' => 'nullable|string|max:255',
            // 'sort_by_name_ar' => 'nullable |string|in:asc,desc',
            // 'sort_by_name_en' => 'nullable |string|in:asc,desc',
            // 'sort_by_address_ar' => 'nullable |string|in:asc,desc',
            // 'sort_by_address_en' => 'nullable |string|in:asc,desc',
            // 'sort_by_id' => 'nullable |string|in:asc,desc',
            'type' => 'required |exists:store_types,id',
            'approved' => 'required ||in:1,0',
        ];
    }

}
