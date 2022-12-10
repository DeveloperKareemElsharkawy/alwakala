<?php

namespace App\Http\Requests\SellerApp\Store;

use App\Rules\General\EGPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateStoreRequest extends FormRequest
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
            'id' => 'required|numeric|exists:stores,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'mobile' => ['required', new EGPhoneNumber, 'size:11'],
            'latitude' => 'required',
            'longitude' => 'required',
            'city_id' => 'required|numeric|exists:cities,id',
            'is_store_has_delivery' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2024',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2024',
            'license' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2024',
        ];
    }
}
