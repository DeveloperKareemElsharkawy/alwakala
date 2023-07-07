<?php

namespace App\Http\Requests\SellerApp\Offer;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\Product\APolicyTypes;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Foundation\Http\FormRequest;

class EditOfferRequest extends FormRequest
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

        $lang = LangHelper::getDefaultLang(request());

        $rules = [
            'id' => 'required|numeric|exists:offers,id,user_id,' . $this->user_id,
            'image' => 'sometimes|image',

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
}
