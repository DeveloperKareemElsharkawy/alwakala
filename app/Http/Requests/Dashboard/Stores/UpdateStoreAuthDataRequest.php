<?php

namespace App\Http\Requests\Dashboard\Stores;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreAuthDataRequest extends FormRequest
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
            'owner_id' => 'required|exists:users,id',
            'email' => 'required|email|unique:users,email,'.request()['owner_id'],
            'mobile' => 'required|unique:users,mobile,'.request()['owner_id'],
            'password' => ''
        ];
    }

}
