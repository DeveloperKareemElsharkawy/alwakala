<?php

namespace App\Http\Requests\SellerApp\Auth;

use App\Rules\General\EGPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConfirmUpdateSellerEmailRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        $userId = $request->user_id;
        $email = $request->email;

        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'confirm_code' => ['required', Rule::exists('user_email_changes', 'confirm_code')->where(function ($query) use ($userId, $email) {
                return $query->where('has_changed', false)->where('user_id', $userId)->where('email', $email);
            })],
        ];

    }

}
