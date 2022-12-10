<?php

namespace App\Http\Requests\SellerApp\Auth;

use App\Rules\General\EGPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;

class UpdateSellerMobileRequest extends FormRequest
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
            'mobile' => ['required', new EGPhoneNumber]
        ];

    }

}
