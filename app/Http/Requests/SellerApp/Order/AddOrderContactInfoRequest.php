<?php

namespace App\Http\Requests\SellerApp\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddOrderContactInfoRequest extends FormRequest
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

            'contact_email' => 'required|email',
            'contact_mobile' => 'required|min:11|max:11',
        ];
    }
}
