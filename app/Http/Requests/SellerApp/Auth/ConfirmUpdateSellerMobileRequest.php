<?php

namespace App\Http\Requests\SellerApp\Auth;

use App\Rules\General\EGPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConfirmUpdateSellerMobileRequest extends FormRequest
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
        $mobile = $request->mobile;

        return [
            'mobile' => ['required', new EGPhoneNumber, 'unique:users,mobile'],
            'confirm_code' => ['required', Rule::exists('user_mobile_changes', 'confirm_code')->where(function ($query) use ($userId, $mobile) {
                return $query->where('has_changed', false)->where('user_id', $userId)->where('mobile', $mobile);
            })],
        ];

    }

}
