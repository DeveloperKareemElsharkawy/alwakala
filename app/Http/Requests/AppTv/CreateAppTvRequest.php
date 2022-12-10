<?php

namespace App\Http\Requests\AppTv;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppTvRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title_en' => 'required|min:4|regex:/[a-zA-Z]/u',
            'title_ar' => 'required|min:4|regex:/[أ-ي]/u',
            'description_en' => 'required|min:4',
            'description_ar' => 'required|min:4',
            'items_ids' => 'required_without:item_id|array',
            'item_id' => 'required_without:items_ids|numeric',
            'item_type' => 'required|numeric|exists:app_tv_types,id',
            'expiry_date' => 'required',
            'web_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'mobile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'app_id' => 'required|numeric|exists:apps,id',
            'category_id' => 'numeric|exists:categories,id',
            'store_id' => 'numeric|exists:stores,id',
            'home_section_id' => 'numeric|exists:home_sections,id',
        ];
    }
}
