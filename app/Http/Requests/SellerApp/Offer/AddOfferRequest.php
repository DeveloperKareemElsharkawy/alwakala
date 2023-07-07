<?php

namespace App\Http\Requests\SellerApp\Offer;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\Product\APolicyTypes;
use App\Enums\Product\AProductStatus;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\StoreId\StoreId;
use Illuminate\Foundation\Http\FormRequest;

class AddOfferRequest extends FormRequest
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
        $lang = LangHelper::getDefaultLang(request());

        $rules = [
            'user_id' => 'required',
            'image' => 'required|image',

            'start_date' => 'required|date|before:end_date|after:yesterday',
            'start_time' => 'required|date_format:H:i',

            'end_date' => 'required|date|after:yesterday',
            'end_time' => 'required|date_format:H:i',

            'discount_type' => 'required|in:' . DiscountTypes::AMOUNT . ',' . DiscountTypes::PERCENTAGE,
            'type' => 'required|in:purchases,bundles',

            'target' => 'required|numeric',
            'discount_value' => 'required',

            'store_id' => 'required|exists:stores,id',
            'products' => 'required|array',
            'products.*' => 'required|numeric|exists:products,id,owner_id,' . $this->user_id . '|exists:products,id,policy_id,' . APolicyTypes::WekalaPrime,
        ];

        $rules['name_' . $lang] = 'required|string|max:255';
        $rules['description_' . $lang] = 'nullable|string';

        return $rules;
    }


    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $storeId = StoreId::getStoreID(request());

        $this->merge([
            'store_id' => $storeId,
            'user_id' => request()->user_id,
        ]);
    }


    public function messages(): array
    {
        return [
            'products.*.product_id.exists' => trans('messages.offers.invalid_product'),
        ];
    }
}
