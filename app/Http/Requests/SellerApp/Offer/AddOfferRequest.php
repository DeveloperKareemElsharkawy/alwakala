<?php

namespace App\Http\Requests\SellerApp\Offer;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\Product\APolicyTypes;
use App\Enums\Product\AProductStatus;
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

        return [
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'required',
            'user_id' => 'required',
            'total_purchased_items' => 'required',
            'from' => 'required|date',
            'to' => 'date',
            'activation' => 'required|boolean',
            'discount_type' => 'required|in:' . DiscountTypes::AMOUNT . ',' . DiscountTypes::PERCENTAGE,
            'discount_value' => 'required_if:discount_type,' . DiscountTypes::PERCENTAGE,
            'bulk_price' => ['required_if:discount_type,' . DiscountTypes::AMOUNT, 'numeric'],
            'retail_price' => ['required_if:discount_type,' . DiscountTypes::AMOUNT, 'numeric'],
            'products' => 'required|array',
            'products.*' => 'required|numeric|exists:products,id,owner_id,' . $this->user_id . '|exists:products,id,policy_id,' . APolicyTypes::WekalaPrime,
         ];
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
            'has_end_date' => (bool)$this->input('to'),
            'type_id' => 1,
            'user_id' => request()->user_id,
            'total_purchased_items' => count(request()->products),
            'products.*.bulk_price' => count(request()->products),
        ]);
    }


    public function messages(): array
    {
        return [
            'products.*.product_id.exists' => trans('messages.offers.invalid_product'),
        ];
    }
}
