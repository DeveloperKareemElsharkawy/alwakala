<?php

namespace App\Http\Requests\Dashboard\HomeSections;

use App\Models\HomeSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHomeSectionRequest extends FormRequest
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
        $selected_orders=array_values(HomeSection::query()->select('order')->where('app_type',request()->app_type)->where('activation',true)->get()->pluck('order')->toArray());

        return [
            'name_ar' => 'required|max:255|min:4',
            'name_en' => 'required|max:255|min:4',
            'item_type' => 'required|min:1|max:2',
            'app_type' => 'required|min:1|max:2',
            'items_ids' => 'required|max:1024',
            'order' => 'required|min:1|max:20|'. Rule::notIn($selected_orders),
            'activation' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ];
    }
}
