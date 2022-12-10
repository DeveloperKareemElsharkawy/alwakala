<?php

namespace App\Http\Requests\SellerApp\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;
use Illuminate\Validation\Rule;

class RegisterSellerRequest extends FormRequest
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
            'name' => "required|string|max:25|min:2",
            'store_name' => "required|string|max:25|min:2",
            'legal_name' => "nullable|string|max:50|min:2",
            'email' => 'email|nullable|unique:users,email|max:255',
            'mobile' => 'required|unique:users,mobile|max:11|min:11|mobile_number', // in other file
            'store_mobile' => 'required|max:11|min:11|mobile_number',
            'store_type_id' => 'required|numeric|exists:store_types,id',
            'password' => 'required|min:8|max:25',
            // 'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            //'licence' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'latitude' => 'required',
            'longitude' => 'required',
            'city_id' => 'required|numeric|exists:cities,id',
            'is_store_has_delivery' => '',
            'user_lang' => 'required',
            'store_categories' => 'required|array',
            'store_categories.*' => 'required|numeric|exists:categories,id',
            'description' => 'nullable|string|max:450|min:2',
            'address' => 'required|string|max:255|min:2',
            'main_branch_store_id' => [ Rule::exists('stores', 'id')->where(function ($query) {
                return $query->where('is_main_branch', true);
            })],
//            'brands' => 'required|array',
//            'brands.*' => 'required|numeric|exists:brands,id'
//            'is_brand' => 'required',
        ];

    }

    public function messages()
    {
        return [
            'name.regex' => trans('messages.auth.name_regex'),
            'store_name.regex' => trans('messages.auth.store_name_regex'),
            'legal_name.regex' => trans('messages.auth.legal_name_regex'),
            'password.regex' => trans('validation.message.pass_hint'),

            'delivery_days.required_without' => trans('seller_validation.required_without'),
            'delivery_hours.required_without' => trans('seller_validation.required_without'),
        ];
    }
}
