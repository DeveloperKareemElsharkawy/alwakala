<?php

namespace App\Http\Requests\SellerApp\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;

class ResetPasswordRequest extends FormRequest
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
        $type = (filter_var(request()->email, FILTER_VALIDATE_EMAIL)) ? 'email' : 'mobile';

        return [
            'email' => 'required|string|exists:users,' . $type
        ];

    }

    public function messages(): array
    {
        $type = (filter_var(request()->email, FILTER_VALIDATE_EMAIL)) ? 'email' : 'mobile';

        $existsValidationMessage = trans('messages.auth.' . $type . '_exists_validation');

        return [
            'email.required' => trans('messages.auth.mobile_or_email_required'),
            'email.exists' => $existsValidationMessage,
        ];
    }


}
