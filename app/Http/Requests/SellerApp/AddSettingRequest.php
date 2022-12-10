<?php

namespace App\Http\Requests\SellerApp;

use Illuminate\Foundation\Http\FormRequest;

class AddSettingRequest extends FormRequest
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
            'website' => 'required_without_all:facebook,instagram,whatsapp,twitter,pinterest,youtube|string|max:255',
            'facebook' => 'required_without_all:website,instagram,whatsapp,twitter,pinterest,youtube|string|max:255',
            'instagram' => 'required_without_all:website,facebook,whatsapp,twitter,pinterest,youtube|string|max:255',
            'whatsapp' => 'required_without_all:website,facebook,instagram,twitter,pinterest,youtube|string|max:255',
            'twitter' => 'required_without_all:website,facebook,instagram,whatsapp,pinterest,youtube|string|max:255',
            'pinterest' => 'required_without_all:website,facebook,instagram,whatsapp,twitter,youtube|string|max:255',
            'youtube' => 'required_without_all:website,facebook,instagram,whatsapp,twitter,pinterest|string|max:255',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'website.required_without_all' => trans('messages.general.at_least_one'),
            'facebook.required_without_all' => trans('messages.general.at_least_one'),
            'instagram.required_without_all' => trans('messages.general.at_least_one'),
            'whatsapp.required_without_all' => trans('messages.general.at_least_one'),
            'twitter.required_without_all' => trans('messages.general.at_least_one'),
            'pinterest.required_without_all' => trans('messages.general.at_least_one'),
            'youtube.required_without_all' => trans('messages.general.at_least_one'),
        ];
    }
}
