<?php

namespace App\Http\Requests\SellerApp\Store;

use App\Rules\General\EGPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateStoreInfoRequest extends FormRequest
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
        return [
            'name' => 'sometimes|string|max:255',
            'seller_name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'mobile' => ['sometimes', new EGPhoneNumber, 'size:11'],
            'latitude' => 'sometimes',
            'longitude' => 'sometimes',
            'store_profile_id' => 'sometimes|unique:stores,store_profile_id',
            'categories' => 'sometimes|array',
            'categories.*' => 'sometimes|numeric|exists:categories,id',
            'city_id' => 'sometimes|numeric|exists:cities,id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2024',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2024',
            'license' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2024',
        ];
    }
}
