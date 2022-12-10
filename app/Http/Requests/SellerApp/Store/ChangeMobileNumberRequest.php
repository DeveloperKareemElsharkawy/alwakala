<?php

namespace App\Http\Requests\SellerApp\Store;

use App\Lib\Helpers\StoreId\StoreId;
use App\Rules\General\EGPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChangeMobileNumberRequest extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request): array
    {
        $storeId = StoreId::getStoreID($request);

        return [
            'mobile' => ['required', new EGPhoneNumber, 'unique:users,mobile'],
        ];
    }

}
