<?php

namespace App\Http\Requests\SellerApp;

use Illuminate\Foundation\Http\FormRequest;

class SyncStoreBadgesRequests extends FormRequest
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
            'id' => 'required|numeric|exists:stores,id',
            'badges' => 'required|array|min:1',
            'badges.*' => 'exists:badges,id',
        ];
    }
}
