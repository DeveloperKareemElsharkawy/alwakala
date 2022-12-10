<?php

namespace App\Http\Requests\SellerApp\TargetOffers;

use Illuminate\Foundation\Http\FormRequest;

class CreateTargetOffersRequest extends FormRequest
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
            'name_ar' => 'required|max:255|min:2',
            'name_en' => 'required|max:255|min:2',
            'description' => 'required|max:450|min:10',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'start_counting_date' => 'required|string',
            'is_active' => 'required|boolean',
            'discount_value' => 'required|numeric',
            'milestones' => 'required|array',
            'milestones.*.targeted_price' => 'required|numeric|min:1|max:1000000',
            'milestones.*.reward_value' => 'required|numeric|min:1|max:100',
            'milestones.*.is_active' => 'required|boolean',
            'products' => 'required|array'
        ];
    }
}
