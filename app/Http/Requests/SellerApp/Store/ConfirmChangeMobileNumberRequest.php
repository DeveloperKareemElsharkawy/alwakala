<?php

namespace App\Http\Requests\SellerApp\Store;

use App\Lib\Helpers\StoreId\StoreId;
use App\Models\Store;
use App\Rules\General\EGPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConfirmChangeMobileNumberRequest extends FormRequest
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
        $mobile = $request->mobile;

        return [
            'mobile' => ['required', new EGPhoneNumber, 'unique:stores,mobile'],
            'confirm_code' => ['required', Rule::exists('store_mobile_changes', 'confirm_code')->where(function ($query) use ($storeId, $mobile) {
                return $query->where('has_changed', false)->where('store_id', $storeId);
            })],
        ];
    }

}
